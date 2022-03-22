<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WC1C_PRESERVE_PRODUCT_VARIATIONS')) {
    define('WC1C_PRESERVE_PRODUCT_VARIATIONS', false);
}

/**
 * @param $xml
 * @param $is_full
 * @param $wc1c_max_interval
 */
function wc1c_read_xml_offers($xml, $is_full, $wc1c_max_interval)
{
    global $wpdb;
    if (isset($xml->ПакетПредложений)) {

        $wc1c_offer_commited = $_SESSION["wc1c_offerCommited"];
        $wc1c_ar_options = wc1c_options::wc1c_get_options();
        //Enterego - id terms catalog cooming_soon_sale
        $wc1c_ar_options['wc1c_new'] = wc1c_term_id_by_meta('wc1c_guid', "product_cat::wc1c_new");
        $wc1c_ar_options['wc1c_soon_sale'] = wc1c_term_id_by_meta('wc1c_guid', "product_cat::soon_sale");
        //

        if (isset($_SESSION["wc1c_last_prod_id"])) {
            $wc1c_sess_last_id_prod = $_SESSION["wc1c_last_prod_id"];
        } else {
            $wc1c_sess_last_id_prod = null;
        }

        $offers = $xml->ПакетПредложений;
        wc1c_read_xml_prices($offers);

        $wc1c_suboffers = array();
        $start_toggle = microtime(true);
        $count_offer = 0;

        if (isset($offers->Предложения)) {
            foreach ($offers->Предложения->Предложение as $offer) {
                $count_offer++;

                if ($count_offer > $wc1c_offer_commited) {

                    $wc1c_offer = array(
                        'ХарактеристикиТовара' => array(),
                    );

                    $wc1c_offer["Ид"] = (string)wc1c_get_xml_value($offer, "Ид");
                    $wc1c_offer["Наименование"] = (string)wc1c_get_xml_value($offer, "Наименование");

                    if (isset($offer->Штрихкод)) {
                        $wc1c_offer["Штрихкод"] = (string)$offer->Штрихкод;
                    }

                    $wc1c_offer = wc1c_read_xml_rest($offer, $wc1c_offer);
                    if (isset($offer->Цены)) {
                        $wc1c_offer["Цены"] = wc1c_read_xml_price($offer);
                    }

                    if (isset($offer->ХарактеристикиТовара)) {
                        $wc1c_offer["ХарактеристикиТовара"] = array();
                        foreach ($offer->ХарактеристикиТовара->ХарактеристикаТовара as $offer_character) {
                            $wc1c_character = array(
                                "Ид" => (string)$offer_character->Ид,
                                "Наименование" => (string)$offer_character->Наименование,
                                "Значение" => (string)$offer_character->Значение
                            );
                            $wc1c_offer["ХарактеристикиТовара"][] = $wc1c_character;
                        }
                    }

                    if (isset($offer->ЗначенияСвойств)) {
                        $wc1c_offer["ЗначенияСвойств"] = array();
                        foreach ($offer->ЗначенияСвойств->ЗначенияСвойства as $offer_propertys) {
                            $wc1c_property = array(
                                "Ид" => (string)$offer_propertys->Ид,
                                "Значение" => (string)$offer_propertys->Значение
                            );
                            $wc1c_offer["ЗначенияСвойств"][] = $wc1c_property;
                        }
                    }

                    $end_toggle = microtime(true);
                    $interval = round(($end_toggle - $start_toggle), 2);
                    if (strpos($wc1c_offer['Ид'], '#') === false || WC1C_DISABLE_VARIATIONS) {
                        $guid = $wc1c_offer['Ид'];
                        wc1c_replace_offer($is_full, $guid, $wc1c_offer);

                        if ($interval >= $wc1c_max_interval) {
                            $_SESSION["wc1c_offerCommited"] = $count_offer;
                            exit("progress\nElements commit:$count_offer\nTimeOffer:$interval");
                        }
                        $wc1c_suboffers = array();
                        $wc1c_sess_last_id_prod = $guid;
                    } else {
                        $guid = $wc1c_offer['Ид'];
                        list($product_guid,) = explode('#', $guid, 2);

                        if ($wc1c_sess_last_id_prod === $product_guid) {
                            $wc1c_suboffers['offers'][] = $wc1c_offer;
                        } else {
                            if ($wc1c_suboffers) {
                                $_SESSION["wc1c_last_prod_id"] = $product_guid;
                                wc1c_replace_suboffers($is_full, $wc1c_suboffers, false, $wc1c_ar_options);

                                if ($interval >= $wc1c_max_interval) {
                                    $_SESSION["wc1c_offerCommited"] = $count_offer - 1;
                                    exit("progress\nElements commit:$count_offer\nTimeOffer:$interval");
                                }
                            }
                            $wc1c_suboffers = array(
                                'product_guid' => $product_guid,
                                'offers' => array($wc1c_offer),
                            );
                            $wc1c_sess_last_id_prod = $product_guid;
                        }
                    }
                }
            }
            if ($wc1c_suboffers) {
                wc1c_replace_suboffers($is_full, $wc1c_suboffers, false, $wc1c_ar_options);
            }
        }
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
        wc1c_check_wpdb_error();

        do_action('wc1c_post_offers', $is_full);
    }
}

/**
 * @param $offer
 * @param $wc1c_offer array
 * @return array
 */
function wc1c_read_xml_rest($offer, $wc1c_offer)
{

    if (isset($offer->Количество)) //old version exchanges
    {
        $wc1c_offer["Количество"] = wc1c_parse_decimal($offer->Количество);
    } elseif (isset($offer->Остатки)) {
        $sum_rest = 0;
        //Enterego - продажи сезона
        $quantity_backorder = 0;
        //$stock_all = 0;
        //Enterego
        foreach ($offer->Остатки->Остаток as $rest_xml) {
            if (isset($rest_xml->Склад)) {
                //Enterego - продажи сезона
                if ($rest_xml->Склад->Ид == 'onbackorder') {
                    $quantity_backorder += wc1c_parse_decimal($rest_xml->Склад->Количество);
                } else {
                    $sum_rest += wc1c_parse_decimal($rest_xml->Склад->Количество);
                }
                //enterego
            } else {
                $sum_rest += wc1c_parse_decimal($rest_xml->Количество);
            }
        }
        //Enterego - продажи сезона
        $wc1c_offer["КоличествоНаСезон"] = $quantity_backorder;
        //Enterego
        $wc1c_offer["Количество"] = $sum_rest;
    }
    return $wc1c_offer;
}

/**
 * @param $wc1c_offer
 * @return array
 */
function wc1c_read_xml_price($wc1c_offer)
{
    $wc1c_prices = array();
    if (isset($wc1c_offer->Цены)) {
        foreach ($wc1c_offer->Цены->Цена as $price) {
            $wc1c_price = array();

            $wc1c_price["Представление"] = isset($price->Представление) ? (string)$price->Представление : '';
            $wc1c_price["ИдТипаЦены"] = isset($price->Представление) ? (string)$price->ИдТипаЦены : '';
            $wc1c_price["ЦенаЗаЕдиницу"] = isset($price->Представление) ? (string)$price->ЦенаЗаЕдиницу : '';
            $wc1c_price["Валюта"] = isset($price->Представление) ? (string)$price->Валюта : '';
            $wc1c_price["Единица"] = isset($price->Представление) ? (string)$price->Единица : '';
            $wc1c_price["Коэффициент"] = isset($price->Представление) ? (string)$price->Коэффициент : '';
            $wc1c_prices[] = $wc1c_price;
        }
    }
    return $wc1c_prices;
}

/**
 * @param $is_full
 * @param $guid
 * @param $offer
 */
function wc1c_replace_offer($is_full, $guid, $offer)
{
    $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);
    if ($post_id) {
        wc1c_replace_offer_post_meta($is_full, $post_id, $offer);
    }

    do_action('wc1c_post_offer', $post_id, $offer, $is_full);
}

function wc1c_replace_suboffers($is_full, $suboffers, $are_products = false, $wc1c_ar_options = array())
{
    if (!$suboffers) {
        return;
    }

    $product_guid = $suboffers['product_guid'];
    $post_id = wc1c_post_id_by_meta('_wc1c_guid', $product_guid);
    if (!$post_id && !$are_products) {
        return;
    }

    //TODO Проверка массива на наличие характеристик для пропуска массивов с ценами и остатками
    //24.10.2018
    foreach ($suboffers['offers'] as $arOffer) {
        if (!empty($arOffer["ХарактеристикиТовара"] || !empty($arOffer["ЗначенияСвойств"]))) {
            wc1c_refresh_variables_offer($post_id, $suboffers['offers']);
        }
    }
    //*****************************************************************************************

    wc1c_update_product($post_id, $suboffers['offers']);

    //Enterego - скоро в продаже
    $product_coming_soon = get_post_meta($post_id, 'coming_soon', true);
    //
    $quantity_summ = 0;
    $quantity_backorder_summ = 0;
    $update_rest = false;
    foreach ($suboffers['offers'] as $arOffer) {
        if (isset($arOffer["Количество"])) {
            $update_rest = true;
            $quantity = wc1c_parse_decimal($arOffer["Количество"]);
            $quantity_summ += $quantity;
        }
        if (isset($arOffer["КоличествоНаСезон"])) {
            $update_rest = true;
            $quantity_backorder = wc1c_parse_decimal($arOffer["КоличествоНаСезон"]);
            $quantity_backorder_summ += $quantity_backorder;
            $quantity_summ += $quantity_backorder;
        }
    }
    if ($update_rest) {
        //Enterego - перемещаем товар в новинки из скоро в продаже если есть остатки
        $quantity_in_stock = $quantity_summ - $quantity_backorder_summ;
        if ($quantity_in_stock > 0) {
            $res = wc1c_update_coming_soon_sale($post_id, $wc1c_ar_options['wc1c_soon_sale'], $wc1c_ar_options['wc1c_new']);
            if ($res)
                update_post_meta($post_id, 'wc1c_datetime_new_category', time());
        }

        update_post_meta($post_id, '_manage_stock', 'yes');
        update_post_meta($post_id, '_stock', $quantity_summ);
        //Enterego
        update_post_meta($post_id, '_backorders_count', $quantity_backorder_summ);

        //Enterego - скоро в продаже
        if ($product_coming_soon === 'true') {
            $stock_status = $quantity_summ > 0 ? 'instock' : 'onbackorder';
            $backorders = 'yes';
        } else {
            $backorders = 'no';
            $stock_status = $quantity_summ > 0 ? 'instock' : 'outofstock';

            if ($update_rest && $quantity_summ == 0) {
                foreach ($suboffers['offers'] as $suboffer) {

                    if ($suboffer["КоличествоНаСезон"] > 0) {
                        $backorders = 'yes';
                        $stock_status = "onbackorder";
                        break;
                    }
                }
            }
        }
        //

        update_post_meta($post_id, '_stock_status', $stock_status);
        update_post_meta($post_id, '_backorders', $backorders);
        // wc1c_update_product_status($post_id, $stock_status);
    }

    if (!WC1C_DISABLE_VARIATIONS) {
        $result = wp_set_post_terms($post_id, 'variable', 'product_type');
        wc1c_check_wp_error($result);
    }

    //TODO Заполнение вариаций на основании списочного аттрибута
    //    $offer_characteristics = array();
    //    foreach ($suboffers as $suboffer) {
    //        if (isset($suboffer['offer']['ХарактеристикиТовара'])) {
    //            foreach ($suboffer['offer']['ХарактеристикиТовара'] as $suboffer_characteristic) {
    //
    //                $characteristic_name = $suboffer_characteristic['Наименование'];
    //                if (!isset($offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name] = array();
    //
    //                $characteristic_value = @$suboffer_characteristic['Значение'];
    //                if (!in_array($characteristic_value, $offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name][] = $characteristic_value;
    //            }
    //        }
    //    }
    //    $product_variation_ids = array();
    //    foreach ($suboffers as $i => $suboffer) {
    //        $product_variation_id = wc1c_replace_product_variation($suboffer['guid'], $post_id, $i + 1);
    //        print_debug_info($product_variation_id, "product_variation_id.txt");
    //        $product_variation_ids[] = $product_variation_id;
    //
    //        $attributes = array_fill_keys(array_keys($offer_characteristics), '');
    //        if (isset($suboffer['offer']['ХарактеристикиТовара'])) {
    //            foreach ($suboffer['offer']['ХарактеристикиТовара'] as $suboffer_characteristic) {
    //                $suboffer_characteristic_value = @$suboffer_characteristic['Значение'];
    //                if ($suboffer_characteristic_value) $attributes[$suboffer_characteristic['Наименование']] = $suboffer_characteristic_value;
    //            }
    //        }
    //
    //    }

    $offer_propertys = array();
    foreach ($suboffers['offers'] as $suboffer) {
        if (isset($suboffer['offer']['ЗначенияСвойств'])) {
            foreach ($suboffer['offer']['ЗначенияСвойств'] as $suboffer_property) {

                $property_id = $suboffer_property['Ид'];
                if (!isset($offer_propertys[$property_id])) {
                    $offer_propertys[$property_id] = array();
                }

                $characteristic_value = @$suboffer_property['Значение'];
                if (!in_array($characteristic_value, $offer_propertys[$property_id])) {
                    $offer_propertys[$property_id][] = $characteristic_value;
                }
            }
        }
    }

    $product_post_meta = array();
    foreach ($suboffers['offers'] as $i => $suboffer) {
        $product_variation_id = wc1c_replace_product_variation($suboffer['Ид'], $post_id, $i + 1);

        $attributes = array_fill_keys(array_keys($offer_propertys), '');
        if (isset($suboffer['ЗначенияСвойств'])) {
            foreach ($suboffer['ЗначенияСвойств'] as $suboffer_characteristic) {
                $suboffer_characteristic_value = $suboffer_characteristic['Значение'];

                if ($suboffer_characteristic_value) {
                    $attributes[$suboffer_characteristic['Ид']] = $suboffer_characteristic_value;
                }
            }
        }
        //Enterego - скоро в продаже
        if ($product_coming_soon === 'true') {
            $price = get_post_meta($product_variation_id, '_price', true);

            if (empty($price)) {
                update_post_meta($product_variation_id, '_price', 0);
                update_post_meta($product_variation_id, '_regular_price', 0);
            }
        }


        $offer_post_meta = wc1c_replace_offer_post_meta($is_full, $product_variation_id, $suboffer, $attributes, $product_coming_soon);

        if (isset($offer_post_meta['_price'])) {

            if (isset($product_post_meta['_price'])) {
                $product_post_meta['_price'] = (min($product_post_meta['_price'], $offer_post_meta['_price']));
            } else {
                $product_post_meta['_price'] = $offer_post_meta['_price'];
            }

            if (isset($offer_post_meta['_sale_price'])) {
                $product_post_meta['_sale_price'] = (min($product_post_meta['_sale_price'], $offer_post_meta['_sale_price']));
                $sale_proc = (($offer_post_meta['_regular_price'] - $offer_post_meta['_sale_price']) / $offer_post_meta['_regular_price']) * 100;
                $product_post_meta['_new_sale_price'] = ceil($sale_proc);
            } else {
                $product_post_meta['_new_sale_price'] = 0;
            }

        }
    }
    if (!empty($product_post_meta['_price'])) {
        update_post_meta($post_id, "_price", $product_post_meta['_price']);
        update_post_meta($post_id, "_new_sale_price", $product_post_meta['_new_sale_price']);
    }

    if ($product_post_meta['_new_sale_price'] !== 0 && isset($product_post_meta['_new_sale_price']) )  {
        $term_ids[] = '49';
        wc1c_update_product_category($post_id, $term_ids,'taxonomy');
    }

    $metaStock = get_post_meta($post_id, 'coming_soon', true);
    $metaDate = get_post_meta($post_id, 'planned_date', true);

    if ($metaStock === 'true' && $metaDate !== 'false')  {
        $term_ids[] = '84';
        wc1c_update_product_category($post_id, $term_ids,'taxonomy');
    }
}

function wc1c_update_currency($currency)
{
    if (!array_key_exists($currency, get_woocommerce_currencies())) {
        return;
    }

    update_option('woocommerce_currency', $currency);

    $currency_position = array(
        'RUB' => 'right_space',
        'USD' => 'left',
    );
    if (isset($currency_position[$currency])) {
        update_option('woocommerce_currency_pos', $currency_position[$currency]);
    }
}

/**
 * @param $is_full
 * @param $post_id
 * @param $offer
 * @param array $attributes
 * product_coming_soon
 */
//function wc1c_replace_offer_post_meta($is_full, $post_id, $offer, $attributes = array())
function wc1c_replace_offer_post_meta($is_full, $post_id, $offer, $attributes = array(), $product_coming_soon = 'false')//enterego скоро в продаже
{
    $post_meta = array();
    $wc1c_option = wc1c_options::wc1c_get_options();

    if (isset($offer['Цены'])) {
        $post_meta['_sale_price'] = null;
        foreach ($offer['Цены'] as $offer_price) {
            $price = isset($offer_price['ЦенаЗаЕдиницу']) ? wc1c_parse_decimal($offer_price['ЦенаЗаЕдиницу']) : null;

            if (!is_null($price)) {
                $coefficient = isset($offer['Цена']['Коэффициент']) ? wc1c_parse_decimal($offer['Цена']['Коэффициент']) : null;
                if (!is_null($coefficient)) {
                    $price *= $coefficient;
                }
            }

            if ($offer_price['ИдТипаЦены'] === $wc1c_option['wc1c_product_regular_price']) {
                $post_meta['_regular_price'] = $price;
                $post_meta['_price'] = isset($post_meta['_price']) ? $post_meta['_price'] : $price;
            } elseif ($offer_price['ИдТипаЦены'] === $wc1c_option['wc1c_product_sale_price']) {
                $post_meta['_sale_price'] = $price;
                $post_meta['_price'] = $price;

            }

        }

    }

    $guids = get_option('wc1c_guid_attributes', array());
    $current_post_meta = get_post_meta($post_id);
    if ($attributes) {
        foreach ($attributes as $attribute_guid => $attribute_guid_value) {
            if (isset($guids[$attribute_guid])) {
                $attribute_id = $guids[$attribute_guid];
                if ($attribute_id) {
                    $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
                    $product_attribute_key = $attribute['taxonomy'];
                    $meta_key = 'attribute_' . sanitize_title($product_attribute_key);
                    $post_meta[$meta_key] = wc1c_term_slug_by_meta('wc1c_guid', "$product_attribute_key::$attribute_guid_value");
                }
            } else {
                if ($attribute_guid === 'barcode') {
                    $meta_key = $attribute_guid;
                    $post_meta[$meta_key] = $attribute_guid_value;
                }
            }
        }

        $current_post_meta = get_post_meta($post_id);
        foreach ($current_post_meta as $meta_key => $meta_value) {
            $current_post_meta[$meta_key] = $meta_value[0];
        }

        foreach ($current_post_meta as $meta_key => $meta_value) {
            if (strpos($meta_key, 'attribute_') !== 0 || array_key_exists($meta_key, $post_meta)) {
                continue;
            }

            delete_post_meta($post_id, $meta_key);
        }
    }

    foreach ($post_meta as $meta_key => $meta_value) {
        $current_meta_value = @$current_post_meta[$meta_key];
        if ($meta_value !== '' && $current_meta_value == $meta_value) {
            continue;
        }
        if ($meta_value === '' && $current_meta_value === $meta_value) {
            continue;
        }

        update_post_meta($post_id, $meta_key, $meta_value);
    }

    if (isset($offer['Количество'])) {

        $quantity = $offer['Количество'];
        if (isset($offer['КоличествоНаСезон'])) {
            $quantity += $offer['КоличествоНаСезон'];
        }
        if (!is_null($quantity)) {
            update_post_meta($post_id, '_stock', $quantity);
            //wc_update_product_stock($post_id, $quantity);

            //Enterego - количество на заказ скоро в продаже
            if ($quantity > 0) {
                $stock_status = 'instock';
                $backorders = 'no';
            } else {
                if ($offer['КоличествоНаСезон'] > 0) {
                    $stock_status = 'onbackorder';
                    $backorders = 'yes';
                } elseif ($product_coming_soon === 'true') {
                    $stock_status = 'onbackorder';
                    $backorders = 'yes';
                } else {
                    $stock_status = 'outofstock';
                    $backorders = 'no';
                }
            }

            $backorders_count = $offer['КоличествоНаСезон'];

            update_post_meta($post_id, '_backorders', $backorders);
            // Enterego

            update_post_meta($post_id, '_stock_status', $stock_status);

            update_post_meta($post_id, '_backorders_count', $backorders_count);

            //@wc_update_product_stock_status($post_id, $stock_status);//долго отрабатывает
        }
    }

    do_action('wc1c_post_offer_meta', $post_id, $offer, $is_full);

    return $post_meta;
}

//enterego
// function wc1c_update_product_status($post_id, $stock_status)
// {
//     $args = array(
//         'ID' => $post_id,
//     );

//     if ($stock_status == 'outofstock') {
//         $args = array_merge($args, array(
//             'post_status' => 'publish',
//         ));
//     } else {
//         $args = array_merge($args, array(
//             'post_status' => 'publish',
//         ));
//     }

//     return wp_update_post($args, true);

// }


function wc1c_update_product($post_id, $arOffers)
{
    $offer_characteristics = array();
    $offer_propertys = array();

    if ($arOffers) {
        foreach ($arOffers as $offer) {
            if (isset($offer["ХарактеристикиТовара"])) {
                foreach ($offer["ХарактеристикиТовара"] as $suboffer_characteristic) {
                    $characteristic_name = $suboffer_characteristic['Наименование'];
                    if (!isset($offer_characteristics[$characteristic_name])) {
                        $offer_characteristics[$characteristic_name] = array();
                    }

                    $characteristic_value = @$suboffer_characteristic['Значение'];
                    if (!in_array($characteristic_value, $offer_characteristics[$characteristic_name])) {
                        $offer_characteristics[$characteristic_name][] = $characteristic_value;
                    }
                }
            }

            if (isset($offer["ЗначенияСвойств"])) {
                foreach ($offer["ЗначенияСвойств"] as $suboffer_property) {
                    $property_id = $suboffer_property['Ид'];
                    if (!isset($offer_propertys[$property_id])) {
                        $offer_propertys[$property_id] = array();
                    }

                    $property_value = @$suboffer_property['Значение'];
                    if (!in_array($property_value, $offer_propertys[$property_id])) {
                        $offer_propertys[$property_id][] = $property_value;
                    }
                }
            }
        }

        //TODO Характеристики предложений
//        if ($offer_characteristics) {
//            ksort($offer_characteristics);
//            foreach ($offer_characteristics as $characteristic_name => &$characteristic_values) {
//                sort($characteristic_values);
//            }
//
//            $current_product_attributes = get_post_meta($post_id, '_product_attributes', true);
//            if (!$current_product_attributes) $current_product_attributes = array();
//
//            $product_attributes = array();
//            foreach ($current_product_attributes as $current_product_attribute_key => $current_product_attribute) {
//                if (!$current_product_attribute['is_variation']) $product_attributes[$current_product_attribute_key] = $current_product_attribute;
//            }
//
//            foreach ($offer_characteristics as $offer_characteristic_name => $offer_characteristic_values) {
//                //TODO Атрибуты в виде списка значений
////                $product_attribute_key = sanitize_title($offer_characteristic_name);
//                $product_attribute_key = sanitize_title('pa_' . strtolower($offer_characteristic_name));
//                $product_attribute_position = count($product_attributes);
//                $product_attributes[$product_attribute_key] = array(
////                    'name' => wc_clean($offer_characteristic_name),
////                    'value' => implode(" | ", $offer_characteristic_values),
//                    'name' => wc_clean('pa_' . strtolower($offer_characteristic_name)),
//                    'value' => '',
//                    'position' => $product_attribute_position,
//                    'is_visible' => 1,
//                    'is_variation' => 1,
//                    'is_taxonomy' => 1,
//                );
//            }
//        }
        $product_attributes = array();
        if ($offer_propertys) {
            $guids = get_option('wc1c_guid_attributes', array());

            $current_product_attributes = get_post_meta($post_id, '_product_attributes', true);

            if (!$current_product_attributes) {
                $current_product_attributes = array();
            }

            foreach ($current_product_attributes as $current_product_attribute_key => $current_product_attribute) {
                if (!$current_product_attribute['is_variation']) {
                    $product_attributes[$current_product_attribute_key] = $current_product_attribute;
                }
            }

            foreach ($offer_propertys as $offer_property_id => $offer_property_values) {
                if (isset($guids[$offer_property_id])) {
                    $attribute_id = $guids[$offer_property_id];

                    if ($attribute_id) {
                        $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);

                        $product_attribute_key = sanitize_title(strtolower($attribute['taxonomy']));
                        $product_attribute_position = count($product_attributes);
                        $product_attributes[$product_attribute_key] = array(
                            'name' => wc_clean(strtolower($attribute['taxonomy'])),
                            'value' => '',
                            'position' => $product_attribute_position,
                            'is_visible' => 1,
                            'is_variation' => 1,
                            'is_taxonomy' => 1,
                        );

                        $taxonomy = $attribute['taxonomy'];
                        $term_ids = array();
                        foreach ($offer_property_values as $values_guid) {
                            $term_id = wc1c_term_taxonomy_id_by_meta('wc1c_guid', "$taxonomy::$values_guid");
                            if ($term_id) {
                                $term_ids[] = $term_id;
                            }
                        }
                        // Enterego
                        $category_all = get_post_meta($post_id, 'add_base_category', true);
                        // added all product in category "all product"
                        if($category_all !== 'true' && $category_all !== true) {
                            $term_ids[] = '86'; // Ид категории "Все товары"
                        }
                        wc1c_update_product_category($post_id, $term_ids, $attribute['taxonomy']);

                    }
                }
            }
        }
        if (!empty($current_product_attributes)) {
            ksort($current_product_attributes);
        }

        $product_attributes_copy = $product_attributes;

        if (isset($current_product_attributes) && isset($product_attributes_copy)) {
            ksort($product_attributes_copy);
            if ($current_product_attributes != $product_attributes_copy) {
                update_post_meta($post_id, '_product_attributes', $product_attributes);
            }
        }
    }
}

function wc1c_refresh_variables_offer($post_id, $arOffers)
{
    $current_product_variation_ids = array();
    $product_variation_posts = get_children("post_parent=$post_id&post_type=product_variation");

    foreach ($product_variation_posts as $product_variation_post) {
        $current_product_variation_ids[] = $product_variation_post->ID;
    }

    $product_variation_ids = array();
    foreach ($arOffers as $offer) {
        $post_id = wc1c_post_id_by_meta('_wc1c_guid', $offer["Ид"]);
        if ($post_id) {
            $product_variation_ids[] = $post_id;
        }
    }

    if (!WC1C_PRESERVE_PRODUCT_VARIATIONS) {
        $deleted_product_variation_ids = array_diff($current_product_variation_ids, $product_variation_ids);
        foreach ($deleted_product_variation_ids as $deleted_product_variation_id) {
            wp_delete_post($deleted_product_variation_id, true);
        }
    }
}

/**
 * @param $guid string
 * @param $parent_post_id
 * @param $order
 * @return bool|int|mixed|null|string|void|WP_Error
 */
function wc1c_replace_product_variation($guid, $parent_post_id, $order)
{
    $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);

    $args = array(
        'menu_order' => $order,
    );

    if (!$post_id) {
        $args = array_merge($args, array(
            'post_type' => 'product_variation',
            'post_parent' => $parent_post_id,
            'post_title' => "Product #$parent_post_id Variation",
            'post_status' => 'publish',
        ));
        $post_id = wp_insert_post($args, true);

        wc1c_check_wpdb_error();
        wc1c_check_wp_error($post_id);

        update_post_meta($post_id, '_manage_stock', 'yes');
        update_post_meta($post_id, '_wc1c_guid', $guid);

        $is_added = true;
    }

    $post = get_post($post_id);
    if (!$post) {
        wc1c_error("Failed to get post");
    }

    if (empty($is_added)) {
        foreach ($args as $key => $value) {
            if ($post->$key == $value) {
                continue;
            }

            $is_changed = true;
            break;
        }

        if (!empty($is_changed)) {
            $args = array_merge($args, array(
                'ID' => $post_id,
            ));
            $post_id = wp_update_post($args, true);
            wc1c_check_wp_error($post_id);
        }
    }

    return $post_id;
}
