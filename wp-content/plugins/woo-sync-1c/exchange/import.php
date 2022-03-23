<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . "wp-admin/includes/media.php";
require_once ABSPATH . "wp-admin/includes/file.php";
require_once ABSPATH . "wp-admin/includes/image.php";

if (!defined('WC1C_PREVENT_CLEAN')) {
    define('WC1C_PREVENT_CLEAN', false);
}

/**
 * @param SimpleXMLElement $xml
 * @param $is_full
 */
function wc1c_read_xml_classifier($xml, $is_full)
{
    if (isset($xml->Классификатор)) {
        $kladr = $xml->Классификатор;

        $wc1c_groups = array();
        $wc1c_group_order = 0;
        $wc1c_parent_id = 0;
        $wc1c_count = 0;

        //Enterego - отключаем загрузку групп
//        if (isset($kladr->Группы)) {
//            foreach ($kladr->Группы->Группа as $wc1c_group) {
//                $wc1c_groups[$wc1c_count] = array(
//                    "Ид" => $wc1c_group->Ид,
//                    "Наименование" => $wc1c_group->Наименование,
//                    "ИдРодителя"
//                );
//                wc1c_replace_term($is_full, (string)$wc1c_group->Ид, (string)$wc1c_parent_id,
//                    (string) $wc1c_group->Наименование, 'product_cat', $wc1c_group_order);
//                $wc1c_groups[$wc1c_count]["Группы"] = wc1c_read_groups($wc1c_group, $wc1c_group->Ид,
//                    $wc1c_group_order + 1);
//                $wc1c_count++;
//            }
//        }
        //**

        wc1c_read_xml_prices($kladr);

        $wc1c_requisite_properties = array();
        $wc1c_property_order = 0;
        if (isset($kladr->Свойства)) {
            foreach ($kladr->Свойства->Свойство as $property) {
                $wc1c_property = array(
                    "Ид" => (string)$property->Ид,
                    "Наименование" => (string)$property->Наименование
                );

                if (isset($property->Описание)) {
                    $wc1c_property["Описание"] = (string)$property->Описание;
                }

                if (isset($property->ТипЗначений)) {
                    $wc1c_property["ТипЗначений"] = (string)$property->ТипЗначений;
                }

                if (isset($property->ВариантыЗначений)) {
                    $count_value = 0;
                    if (isset($property->ВариантыЗначений->Справочник)) {
                        $wc1c_property['ВариантыЗначений'] = array();
                        foreach ($property->ВариантыЗначений->Справочник as $property_vlaue) {
                            $wc1c_property['ВариантыЗначений'][$count_value] = array(
                                "ИдЗначения" => (string)$property_vlaue->ИдЗначения,
                                "Значение" => (string)$property_vlaue->Значение
                            );
                            $count_value++;
                        }
                    }

                    if (isset($property->ВариантыЗначений->Значение)) {
                        if (!isset($wc1c_property['ВариантыЗначений'])) {
                            $wc1c_property['ВариантыЗначений'] = array();
                        }

                        foreach ($property->ВариантыЗначений->Значение as $property_vlaue) {
                            $wc1c_property['ВариантыЗначений'][$count_value] = array("Значение" => (string)$property_vlaue->Значение);
                            $count_value++;
                        }
                    }
                }

                $result = wc1c_replace_property($is_full, $wc1c_property, $wc1c_property_order);
                if ($result) {
                    $attribute_taxonomy = $result;
                    $wc1c_property_order++;

                    wc1c_clean_woocommerce_attribute_options($is_full, $attribute_taxonomy);
                } else {
                    $wc1c_requisite_properties[$wc1c_property['Ид']] = $wc1c_property;
                }
            }
        }

        wc1c_clean_woocommerce_attributes($is_full);
        delete_transient('wc_attribute_taxonomies');
    }
}

/**
 * @param SimpleXMLElement $cml_block
 */
function wc1c_read_xml_prices($cml_block)
{
    $options = wc1c_options::wc1c_get_options();

    if (isset($cml_block->ТипыЦен)) {

        $wc1c_price_types = get_option('wc1c_prices', array());
        foreach ($cml_block->ТипыЦен->ТипЦены as $cml_price) {
            $price_guid = (string)$cml_price->Ид;
            $wc1c_price_types[$price_guid] = array(
                "Наименование" => (string)$cml_price->Наименование,
                "Валюта" => (string)$cml_price->Валюта
            );
            if (empty($options['wc1c_product_regular_price'])) {
                $options['wc1c_product_regular_price'] = $price_guid;
                wc1c_options::wc1c_save_options($options);
            }
        }

        update_option('wc1c_prices', $wc1c_price_types);
    }
}

function wc1c_read_xml_catalog($xml, $is_full, $wc1c_max_interval, $time_kladr_xml)
{
    if (isset($xml->Каталог)) {
        $catalog = $xml->Каталог;

        foreach ($catalog->attributes() as $cml_attr_key => $cml_attr_value) {
            if ($cml_attr_key === "СодержитТолькоИзменения" && $cml_attr_value == 'false') {
                $is_full = true;
            }
        }

        $wc1c_ar_options = wc1c_options::wc1c_get_options();

        $wc1c_product_commited = $_SESSION["wc1c_productCommited"];
        $count_product = 0;
        $start_toggle = microtime(true);
        if (isset($catalog->Товары)) {
            foreach ($catalog->Товары->Товар as $product) {
                $count_product++;
                if ($count_product > $wc1c_product_commited) {

                    $wc1c_product = array();
                    $wc1c_product["Ид"] = (string)$product->Ид;
                    $wc1c_product["Наименование"] = (string)$product->Наименование;

                    if (isset($product->Штрихкод)) {
                        $wc1c_product["Штрихкод"] = (string)$product->Штрихкод;
                    }

                    if (isset($product->Артикул)) {
                        $wc1c_product["Артикул"] = (string)$product->Артикул;
                    }

                    if (isset($product->Описание)) {
                        $wc1c_product["Описание"] = (string)$product->Описание;
                    }

                    $wc1c_product["Группы"] = array();
                    if (isset($product->Группы) && isset($product->Группы->Ид)) {
                        foreach ($product->Группы->Ид as $product_group_id) {
                            $wc1c_product["Группы"][] = (string)$product_group_id;
                        }
                    }

                    $wc1c_product["ЗначенияСвойств"] = array();
                    if (isset($product->ЗначенияСвойств)) {

                        foreach ($product->ЗначенияСвойств->ЗначенияСвойства as $product_property) {
                            //Enterego Скоро в продаже
                            $property_id = (string)$product_property->Ид;
                            if ($property_id === 'coming_soon') {
                                $wc1c_product['coming_soon'] = (string)$product_property->Значение;
                                continue;
                            }
                            //Enterego
                            //Enterego категория Все товары
                            if ($property_id === 'add_base_category') {
                                $wc1c_product['add_base_category'] = (string)$product_property->Значение;
                                continue;
                            }
                            //Enterego

                            $wc1c_property = array("Ид" => (string)$property_id, "Значение" => array());

                            foreach ($product_property->Значение as $product_property_value) {
                                $wc1c_property["Значение"][] = (string)$product_property_value;
                            }

                            $wc1c_product["ЗначенияСвойств"][] = $wc1c_property;
                        }
                    }

                    $wc1c_product["Картинка"] = array();
                    if (isset($product->Картинка)) {
                        foreach ($product->Картинка as $product_image) {
                            $wc1c_product["Картинка"][] = (string)$product_image;
                        }
                    }

                    $wc1c_product["ХарактеристикиТовара"] = array();
                    if (isset($product->ХарактеристикиТовара)) {
                        foreach ($product->ХарактеристикиТовара->ХарактеристикаТовара as $product_offer) {
                            $wc1c_offer = array(
                                "Ид" => (string)$product_offer->Ид,
                                "Наименование" => (string)$product_offer->Наименование,
                                "Значение" => (string)$product_offer->Значение
                            );
                            $wc1c_product["ХарактеристикиТовара"][] = $wc1c_offer;
                        }
                    }

                    $wc1c_product["ЗначенияРеквизитов"] = array();
                    if (isset($product->ЗначенияРеквизитов)) {
                        foreach ($product->ЗначенияРеквизитов->ЗначениеРеквизита as $product_value) {
                            $wc1c_value = array(
                                "Наименование" => (string)$product_value->Наименование,
                                "Значение" => array()
                            );
                            $wc1c_value["Значение"][] = (string)$product_value->Значение;
                            $wc1c_product["ЗначенияРеквизитов"][] = $wc1c_value;
                        }
                    }

                    $wc1c_subproducts = false;
                    if (strpos($wc1c_product['Ид'], '#') === false || WC1C_DISABLE_VARIATIONS) {
                        $guid = $wc1c_product['Ид'];
                        wc1c_replace_product($is_full, $guid, $wc1c_product, $wc1c_ar_options);
                    } else {
                        $guid = $wc1c_product['Ид'];
                        list($product_guid,) = explode('#', $guid, 2);

                        if (empty($wc1c_subproducts) || $wc1c_subproducts[0]['product_guid'] != $product_guid) {
                            if ($wc1c_subproducts) {
                                wc1c_replace_subproducts($is_full, $wc1c_subproducts, $wc1c_ar_options);
                            }
                            $wc1c_subproducts = array();
                        }

                        $wc1c_subproducts[] = array(
                            'guid' => $wc1c_product['Ид'],
                            'product_guid' => $product_guid,
                            'characteristics' => $wc1c_product['ХарактеристикиТовара'],
                            'is_full' => $is_full,
                            'product' => $wc1c_product,
                        );

                        if ($wc1c_subproducts) {
                            wc1c_replace_subproducts($is_full, $wc1c_subproducts, $wc1c_ar_options);
                        }
                    }
                }

                $end_toggle = microtime(true);
                $interval = round(($end_toggle - $start_toggle), 2);

                if ($interval >= $wc1c_max_interval) {
                    $_SESSION["wc1c_productCommited"] = $count_product;
                    exit("progress\nElements commit:$count_product\nTimeKladr:$time_kladr_xml\nTimeCatalog:$interval");
                }
            }
        }

        //wc1c_clean_products($is_full);
        //wc1c_clean_product_terms();
    }
}

function wc1c_term_id_by_meta_cache($key, $value)
{
    global $wpdb;

    if ($value === null) {
        return;
    }

    $cache_key = "wc1c_term_id_by_meta-$key-$value";
    $term_id = wp_cache_get($cache_key);
    if ($term_id === false) {
        $term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->termmeta WHERE meta_key = %s AND meta_value = %s",
            $key, $value));
        wc1c_check_wpdb_error();

        if ($term_id) {
            wp_cache_set($cache_key, $term_id);
        }
    }

    return $term_id;
}

function wc1c_term_id_by_meta($key, $value)
{
    global $wpdb;

    if ($value === null) {
        return;
    }

    $cache_key = "wc1c_term_id_by_meta-$key-$value";
    //$term_id = wp_cache_get($cache_key);
    //if ($term_id === false) {

    //Enterego Правка на получение терм таксономии
    $term_id = $wpdb->get_var($wpdb->prepare("SELECT tt.term_taxonomy_id FROM $wpdb->termmeta  as tm 
        INNER JOIN $wpdb->term_taxonomy as tt ON tm.term_id = tt.term_id WHERE meta_key = %s AND meta_value = %s",
        $key, $value));
    wc1c_check_wpdb_error();

    //if ($term_id) wp_cache_set($cache_key, $term_id);
    //}

    return $term_id;
}

function wc1c_term_taxonomy_id_by_meta($key, $value)
{
    global $wpdb;

    if ($value === null) {
        return;
    }

    $cache_key = "wc1c_term_id_by_meta-$key-$value";
    //$term_id = wp_cache_get($cache_key);
    //if ($term_id === false) {
    $term_id = $wpdb->get_var($wpdb->prepare("SELECT tt.term_taxonomy_id FROM $wpdb->termmeta as termmeta LEFT JOIN $wpdb->term_taxonomy as tt ON termmeta.term_id = tt.term_id  WHERE meta_key = %s AND meta_value = %s",
        $key, $value));
    wc1c_check_wpdb_error();

    //if ($term_id) wp_cache_set($cache_key, $term_id);
    //}

    return $term_id;
}

function wc1c_term_slug_by_meta($key, $value)
{
    global $wpdb;

    if ($value === null) {
        return;
    }

    $term_id = $wpdb->get_var($wpdb->prepare("SELECT tm.slug FROM $wpdb->termmeta tt 
          LEFT JOIN $wpdb->terms tm ON tt.term_id = tm.term_id WHERE tt.meta_key = %s AND tt.meta_value = %s",
        $key, $value));
    wc1c_check_wpdb_error();

    return $term_id;
}

function wc1c_unique_term_name($name, $taxonomy, $parent = null)
{
    global $wpdb;

    $name = htmlspecialchars($name);

    $sql = "SELECT * FROM $wpdb->terms NATURAL JOIN $wpdb->term_taxonomy WHERE name = %s AND taxonomy = %s AND parent = %d LIMIT 1";
    if (!$parent) {
        $parent = 0;
    }
    $term = $wpdb->get_row($wpdb->prepare($sql, $name, $taxonomy, $parent));
    wc1c_check_wpdb_error();
    if (!$term) {
        return $name;
    }

    $number = 2;
    while (true) {
        $new_name = "$name ($number)";
        $number++;

        $term = $wpdb->get_row($wpdb->prepare($sql, $new_name, $taxonomy, $parent));
        wc1c_check_wpdb_error();
        if (!$term) {
            return $new_name;
        }
    }
}

function wc1c_unique_term_slug($name, $taxonomy, $term_id = 0, $slug = '')
{
    global $wpdb;

    if ($slug)
        $sanitized_slug = $slug;
    else {
        while (true) {
            $sanitized_slug = sanitize_title($name);
            if (strlen($sanitized_slug) <= 195) break;

            $name = mb_substr($name, 0, mb_strlen($name) - 3);
        }
    }

    $sql = "SELECT * FROM $wpdb->terms NATURAL JOIN $wpdb->term_taxonomy WHERE slug = %s AND taxonomy = %s AND term_id != %d LIMIT 1";
    $term = $wpdb->get_row($wpdb->prepare($sql, $sanitized_slug, $taxonomy, $term_id));
    wc1c_check_wpdb_error();
    if (!$term) return $sanitized_slug;

    $number = 1;
    while (true) {
        $new_sanitized_slug = "$sanitized_slug-$number";
        $number++;
        $term = $wpdb->get_row($wpdb->prepare($sql, $new_sanitized_slug, $taxonomy, $term_id));
        wc1c_check_wpdb_error();
        if (!$term) return $new_sanitized_slug;
    }
}

function wc1c_wp_unique_term_slug($slug, $term, $original_slug)
{
    if (mb_strlen($slug) <= 200) {
        return $slug;
    }

    do {
        $slug = urldecode($slug);
        $slug = mb_substr($slug, 0, mb_strlen($slug) - 1);
        $slug = urlencode($slug);
        $slug = wp_unique_term_slug($slug, $term);
    } while (mb_strlen($slug) > 200);

    return $slug;
}

add_filter('wp_unique_term_slug', 'wc1c_wp_unique_term_slug', 10, 3);

/**
 * @param bool $is_full
 * @param string $guid
 * @param string $parent_guid
 * @param string $name
 * @param string $taxonomy
 * @param int $order
 * @param bool $use_guid_as_slug
 * @return int|null
 */
function wc1c_replace_term($is_full, $guid, $parent_guid, $name, $taxonomy, $order, $use_guid_as_slug = false)
{
    $term_id = wc1c_term_id_by_meta('wc1c_guid', "$taxonomy::$guid");
    $parent = $parent_guid ? wc1c_term_id_by_meta('wc1c_guid', "$taxonomy::$parent_guid") : null;

    $is_added = false;
    if (!$term_id) {
        $slug = wc1c_unique_term_slug($name, $taxonomy, $parent);
        $args = array(
            'slug' => $slug,
            'parent' => $parent,
        );
        if ($use_guid_as_slug) {
            $args['slug'] = $guid;
        }
        $result = wp_insert_term($name, $taxonomy, $args);
        wc1c_check_wpdb_error();
        wc1c_check_wp_error($result);

        $term_id = $result['term_id'];
        update_term_meta($term_id, 'wc1c_guid', "$taxonomy::$guid");

        $is_added = true;
    } else {
        $args = array();
        $term = get_term($term_id, $taxonomy);
        if (trim($name) != $term->name) {
            $args['name'] = $name;
        }

        if ($term->parent !== $parent)
            $args['parent'] = $parent;
        if (!empty($args)) {
            $result = wp_update_term($term_id, $taxonomy, $args);
            wc1c_check_wp_error($result);
        }
    }

    if ($is_added) {
        $order = wp_count_terms($taxonomy);
        wc_set_term_order($term_id, $order, $taxonomy);
    }

    update_term_meta($term_id, 'wc1c_timestamp', WC1C_TIMESTAMP);

    return $term_id;
}

function wc1c_unique_woocommerce_attribute_name($attribute_label)
{
    global $wpdb;

    $attribute_name = wc_sanitize_taxonomy_name($attribute_label);
    $max_length = 32 - strlen('pa_') - strlen('-00');
    while (strlen($attribute_name) > $max_length) {
        $attribute_name = mb_substr($attribute_name, 0, mb_strlen($attribute_name) - 1);
    }

    $sql = "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s";
    $attribute = $wpdb->get_row($wpdb->prepare($sql, $attribute_name));
    wc1c_check_wpdb_error();
    if (!$attribute) {
        return $attribute_name;
    }

    $number = 2;
    while (true) {
        $new_attribute_name = "$attribute_name-$number";
        $number++;

        $attribute = $wpdb->get_row($wpdb->prepare($sql, $new_attribute_name));
        if (!$attribute) {
            return $new_attribute_name;
        }
    }
}

function wc1c_replace_woocommerce_attribute(
    $is_full,
    $guid,
    $attribute_label,
    $attribute_type,
    $order,
    $preserve_fields
)
{
    global $wpdb;

    $guids = get_option('wc1c_guid_attributes', array());
    $attribute_id = @$guids[$guid];

    if ($attribute_id) {
        $attribute_id = $wpdb->get_var($wpdb->prepare("SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d",
            $attribute_id));
        wc1c_check_wpdb_error();
    }

    $data = compact('attribute_label', 'attribute_type');

    if (!$attribute_id) {
        $attribute_name = wc1c_unique_woocommerce_attribute_name($attribute_label);
        $data = array_merge($data, array(
            'attribute_name' => $attribute_name,
            'attribute_orderby' => 'menu_order',
        ));
        $wpdb->insert("{$wpdb->prefix}woocommerce_attribute_taxonomies", $data);
        wc1c_check_wpdb_error();

        $attribute_id = $wpdb->insert_id;
        $is_added = true;

        $guids[$guid] = $attribute_id;
        update_option('wc1c_guid_attributes', $guids);
    }

    if (empty($is_added)) {
        if (in_array('label', $preserve_fields)) {
            unset($data['attribute_label']);
        }

        if (isset($data['attribute_type'])) {
            unset($data['attribute_type']);
        }

        $wpdb->update("{$wpdb->prefix}woocommerce_attribute_taxonomies", $data, compact('attribute_id'));
        wc1c_check_wpdb_error();
    }

    if ($is_full) {
        $orders = get_option('wc1c_order_attributes', array());
        $order_index = array_search($attribute_id, $orders) or 0;
        if ($order_index !== false) {
            unset($orders[$order_index]);
        }
        array_splice($orders, $order, 0, $attribute_id);
        update_option('wc1c_order_attributes', $orders);
    }

    $timestamps = get_option('wc1c_timestamp_attributes', array());
    $timestamps[$guid] = WC1C_TIMESTAMP;
    update_option('wc1c_timestamp_attributes', $timestamps);

    return $attribute_id;
}

function wc1c_replace_property_option($property_option, $attribute_taxonomy, $order)
{
    if (!isset($property_option['ИдЗначения'], $property_option['Значение'])) {
        return;
    }

    if (!empty($property_option['Значение'])) {
        wc1c_replace_term(true, $property_option['ИдЗначения'], null, $property_option['Значение'],
            $attribute_taxonomy,
            $order, false);
    }
}

function wc1c_replace_property($is_full, $property, $order)
{
    $property = apply_filters('wc1c_import_property_xml', $property, $is_full);
    if (!$property) {
        return;
    }

    $preserve_fields = apply_filters('wc1c_import_preserve_property_fields', array(), $property, $is_full);

    $attribute_name = !empty($property['Наименование']) ? $property['Наименование'] : $property['Ид'];
    $attribute_type = (empty($property['ТипЗначений']) || $property['ТипЗначений'] == 'Справочник' || defined('WC1C_MULTIPLE_VALUES_DELIMETER')) ? 'select' : 'text';
    $attribute_id = wc1c_replace_woocommerce_attribute($is_full, $property['Ид'], $attribute_name, $attribute_type,
        $order, $preserve_fields);

    $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
    if (!$attribute) {
        wc1c_error("Failed to get attribute");
    }

    register_taxonomy($attribute['taxonomy'], null);

    if ($attribute_type == 'select' && !empty($property['ВариантыЗначений'])) {
        foreach ($property['ВариантыЗначений'] as $i => $property_option) {
            wc1c_replace_property_option($property_option, $attribute['taxonomy'], $i + 1);
        }
    }

    return $attribute['taxonomy'];
}

/**
 * @param $if_full boolean
 * @param $guid string
 * @param $args array
 * @param $post_name string
 * @param $post_meta array
 * @param $category_guids array
 * @param $preserve_fields array
 * @param $wc1c_ar_options array
 * @return array
 */
function wc1c_replace_post($if_full, $guid, $args, $post_name, $post_meta, $category_guids, $preserve_fields, $wc1c_ar_options)
{
    $category_taxonomy = 'product_cat';
    $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);

    if (!$post_id) {
        $args = array_merge($args, array(
            'post_name' => $post_name,
            'post_status' => $wc1c_ar_options['wc1c_new_post_status'],
        ));

        $post_id = wp_insert_post($args, true);
        wc1c_check_wpdb_error();
        wc1c_check_wp_error($post_id);

        update_post_meta($post_id, '_visibility', 'visible');
        update_post_meta($post_id, '_wc1c_guid', $guid);

        $is_added = true;
    } else {
        $is_added = false;
        if ($wc1c_ar_options['wc1c_action_after_full_load'] === 'deactivate') {
            $args['post_status'] = $wc1c_ar_options['wc1c_new_post_status'];
        }
    }
    //Enterego
    akimbo_wc1c_update_catalog($post_id,$is_added);

    $post = get_post($post_id);
    if (!$post) {
        wc1c_error("Failed to get post");
    }

    if (!$is_added) {
        if (in_array('title', $preserve_fields)) {
            unset($args['post_title']);
        }
        if (in_array('excerpt', $preserve_fields)) {
            unset($args['post_excerpt']);
        }
        if (in_array('body', $preserve_fields)) {
            unset($args['post_content']);
        }

        foreach ($args as $key => $value) {
            if ($post->$key == $value) {
                continue;
            }

            $is_changed = true;
            break;
        }

        if (!empty($is_changed)) {
            $post_date = current_time('mysql');
            $args = array_merge($args, array(
                'ID' => $post_id,
                'post_date' => $post_date,
                'post_date_gmt' => get_gmt_from_date($post_date),
            ));
            $post_id = wp_update_post($args, true);
            wc1c_check_wp_error($post_id);
        }
    }

//        if ($is_deleted && $post->post_status != 'trash') {
//            wp_trash_post($post_id);
//        } elseif (!$is_deleted && $post->post_status == 'trash') {
//            wp_untrash_post($post_id);
//        }

    $current_post_meta = get_post_meta($post_id);
    foreach ($current_post_meta as $meta_key => $meta_value) {
        $current_post_meta[$meta_key] = $meta_value[0];
    }

    foreach ($post_meta as $meta_key => $meta_value) {
        $current_meta_value = @$current_post_meta[$meta_key];
        if ($current_meta_value == $meta_value) {
            continue;
        }

        update_post_meta($post_id, $meta_key, $meta_value);
    }

    //Enterego Отключаем загрузку категорий
//        if (!in_array('categories', $preserve_fields)) {
//            $current_category_ids = wp_get_post_terms($post_id, $category_taxonomy, "fields=ids");
//            wc1c_check_wp_error($current_category_ids);
//
//            $category_ids = array();
//            if ($category_guids) {
//                foreach ($category_guids as $category_guid) {
//                    $category_id = wc1c_term_id_by_meta('wc1c_guid', "product_cat::$category_guid");
//                    if ($category_id) {
//                        $category_ids[] = $category_id;
//                    }
//                }
//            }
//
//            sort($current_category_ids);
//            sort($category_ids);
//            if ($current_category_ids != $category_ids) {
//                if ($current_category_ids) {
//                    $result = wp_set_post_terms($post_id, $category_ids, $category_taxonomy);
//                    wc1c_check_wp_error($result);
//                } else {
//                    wc1c_update_product_category($post_id, $category_ids);
//                }
//            }
//        }

    if ($wc1c_ar_options['wc1c_action_after_full_load'] !== 'nothing') {
        if ($if_full) {
            update_post_meta($post_id, '_wc1c_import', true);
        } else {
            update_post_meta($post_id, '_wc1c_timestamp', WC1C_TIMESTAMP);
        }
    }

    return array($is_added, $post_id, $current_post_meta);
}

function deleteCategoryAll($post_id){
    global $wpdb;

    $wpdb->query(
        $wpdb->prepare("DELETE FROM $wpdb->term_relationships WHERE  term_taxonomy_id='86' AND object_id=%d",$post_id)
    );

}
#region Category

function wc1c_update_product_category($post_id, $category_ids, $taxonomy = 'product_cat')
{
    global $wpdb;

    $wpdb->query(
        $wpdb->prepare("DELETE tr FROM $wpdb->term_relationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                      AND tt.taxonomy = '$taxonomy'
                      WHERE object_id=%d", $post_id)
    );

    foreach ($category_ids as $category_id) {
        $wpdb->query(
            $wpdb->prepare("INSERT $wpdb->term_relationships (object_id,term_taxonomy_id,term_order)  VALUES(%d,%d,%d)",
                $post_id, $category_id, 0)
        );

        $count = $wpdb->get_var(
            $wpdb->prepare("SELECT  COUNT(tr.object_id) FROM  $wpdb->term_relationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '$taxonomy'
                      WHERE  tr.term_taxonomy_id=%d GROUP BY tr.term_taxonomy_id", $category_id)
        );
        if (empty($count)) {
            $count = 0;
        }

        $wpdb->query(
            $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count=%d WHERE term_taxonomy_id = %d", $count,
                $category_id)
        );
    };
}

#endregion

function wc1c_replace_post_attachments($post_id, $attachments)
{
    $data_dir = WC1C_DATA_DIR . "catalog";
    $attachment_path_by_hash = array();
    foreach ($attachments as $attachment_path => $attachment) {
        $attachment_path = "$data_dir/$attachment_path";

        $attachment_hash = basename($attachment_path);
        $attachment_path_by_hash[$attachment_hash] = $attachment_path;
    }
    $attachment_hash_by_path = array_flip($attachment_path_by_hash);

    //Enterego
    //$post_attachments = get_attached_media('image', $post_id);
    $post_attachment_id_by_hash = array();
    $post_attachments =get_post_meta( $post_id, '_thumbnail_id');
    $post_attachments = array_merge($post_attachments,explode(',',get_post_meta($post_id,'_product_image_gallery',true)));
    $other_attachments=array();
    foreach ($post_attachments as $post_attachment_id) {
        $post_attachment_path = get_attached_file($post_attachment_id, true);
        //Enterego не удаляем не привязанные к 1с Изображения
        $attachment_ids[] = $post_attachment_id;
        if (file_exists($post_attachment_path)) {
            $post_attachment_hash = basename($post_attachment_path);
            $post_attachment_id_by_hash[$post_attachment_hash] = $post_attachment_id;

            if (isset($attachment_path_by_hash[$post_attachment_hash])) {
                unset($attachment_path_by_hash[$post_attachment_hash]);
                continue;
            }
            else{
                $other_attachments[]=$post_attachment_id;
            }
        }
//        $result = wp_delete_attachment($post_attachment->ID);
//        if ($result === false) {
//            wc1c_error("Failed to delete post attachment");
//        }
    }
    //

    $attachment_ids = array();
    foreach ($attachments as $attachment_path => $attachment) {
        $attachment_path = "$data_dir/$attachment_path";
        $attachment_hash = $attachment_hash_by_path[$attachment_path];
        $attachment_id = @$post_attachment_id_by_hash[$attachment_hash];

        if (!$attachment_id) {
            if (!file_exists($attachment_path)) {
                continue;
            }

            $file = array(
                'tmp_name' => $attachment_path,
                'name' => basename($attachment_path),
            );
            $attachment_id = @media_handle_sideload($file, $post_id, @$attachment['description']);
            wc1c_check_wp_error($attachment_id);

            $uploaded_attachment_path = get_attached_file($attachment_id);
            if ($uploaded_attachment_path) {
                copy($uploaded_attachment_path, $attachment_path);
            }
        }
        $attachment_ids[] = $attachment_id;
    }
    $attachment_ids = array_merge($attachment_ids,$other_attachments);
    return $attachment_ids;
}

function wc1c_replace_requisite_name_callback($matches)
{
    return ' ' . mb_convert_case($matches[0], MB_CASE_LOWER, "UTF-8");
}

function wc1c_replace_product($is_full, $guid, $product, $wc1c_ar_options)
{
    global $wc1c_is_moysklad;

    $product = apply_filters('wc1c_import_product_xml', $product, $is_full);
    if (!$product) {
        return;
    }

    $preserve_fields = apply_filters('wc1c_import_preserve_product_fields', array(), $product, $is_full);

    $post_title = @$product['Наименование'];
    if (!$post_title) {
        return;
    }

    $post_content = '';
    foreach ($product['ЗначенияРеквизитов'] as $i => $requisite) {
        if ($requisite['Наименование'] == "Полное наименование" && @$requisite['Значение'][0]) {
            $value = $requisite['Значение'][0];
            //Ennterego title из наименования
//                if ($wc1c_is_moysklad) {
//                    $post_content = $value;
//                } else {
//                    $post_title = $value;
//                }
            unset($product['ЗначенияРеквизитов'][$i]);
        }
        if ($requisite['Наименование'] == "ВидНоменклатуры"
            || $requisite['Наименование'] == "ТипНоменклатуры"
            || $requisite['Наименование'] == "Планируемая дата поступления") {
            unset($product['ЗначенияРеквизитов'][$i]);
        } elseif ($requisite['Наименование'] == "ОписаниеВФорматеHTML" && @$requisite['Значение'][0]) {
            $post_content = $requisite['Значение'][0];
            unset($product['ЗначенияРеквизитов'][$i]);
        }
    }

    $post_meta = array(
        '_sku' => @$product['Артикул'],
        '_manage_stock' => 'yes',
    );

    $post_name = sanitize_title($post_title);
    $post_name = apply_filters('wc1c_import_product_slug', $post_name, $product, $is_full);



    $args = array(
        'post_type' => 'product',
        'post_title' => $post_title,
        //'post_content' => $post_content enterego отключаем обновления реквизита
    );
    if (isset($product['Описание']) && !empty($product['Описание']) ) {
        $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);
        $field = get_post_field( 'post_excerpt', $post_id);
        if(empty($field)){
            $args['post_excerpt'] = $product['Описание'];
        }
    }

    //Enterego - скоро в продаже
    $post_meta['coming_soon'] = (isset($product['coming_soon'])) ? $product['coming_soon'] : 'false';
    if (!empty($product['add_base_category'])) {
        $post_meta['add_base_category'] = $product['add_base_category'];
    } else {
        $post_meta['add_base_category'] = 'false';
    }
    //

    list($is_added, $post_id, $post_meta) = wc1c_replace_post($is_full, $guid, $args, $post_name, $post_meta, $product['Группы'], $preserve_fields, $wc1c_ar_options);

    // if (isset($product['Пересчет']['Единица'])) {
    //   $quantity = wc1c_parse_decimal($product['Пересчет']['Единица']);
    //   if (isset($product['Пересчет']['Коэффициент'])) $quantity *= wc1c_parse_decimal($product['Пересчет']['Коэффициент']);
    //   wc_update_product_stock($post_id, $quantity);
    //
    //   $stock_status = $quantity > 0 ? 'instock' : 'outofstock';
    //   wc_update_product_stock_status($post_id, $stock_status);
    // }

    $current_product_attributes = isset($post_meta['_product_attributes']) ? maybe_unserialize($post_meta['_product_attributes']) : array();

    $product_attributes = array();

    /**
     * Enterego | Zhukov
     */
    $temp_attr = ['pa_color', 'pa_structure', 'pa_size', 'pa_rost-fotomodeli', 'pa_podkladka'];
    foreach($current_product_attributes as $current_product_attributes_key => $current_product_attributes_val) {
        if(in_array($current_product_attributes_key, $temp_attr)) {
            $product_attributes[$current_product_attributes_key] = $current_product_attributes_val;
        }
    }

    $current_product_attribute_variations = array();
    foreach ($current_product_attributes as $current_product_attribute_key => $current_product_attribute) {
        if (!$current_product_attribute['is_variation']) {
            continue;
        }

        unset($current_product_attributes[$current_product_attribute_key]);
        $current_product_attribute_variations[$current_product_attribute_key] = $current_product_attribute;
    }

    $product_attribute_values = array();
    if (!empty($product['Изготовитель']['Наименование'])) {
        $product_attribute_values["Наименование изготовителя"] = $product['Изготовитель']['Наименование'];
    }
    if (!empty($product['БазоваяЕдиница']) && trim($product['БазоваяЕдиница'])) {
        $product_attribute_values["Базовая единица"] = trim($product['БазоваяЕдиница']);
    }

    foreach ($product_attribute_values as $product_attribute_name => $product_attribute_value) {
        $product_attribute_key = sanitize_title($product_attribute_name);
        $product_attribute_position = count($product_attributes);
        $product_attributes[$product_attribute_key] = array(
            'name' => wc_clean($product_attribute_name),
            'value' => $product_attribute_value,
            'position' => $product_attribute_position,
            'is_visible' => 0,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
    }

    if ($product['ЗначенияСвойств']) {
        $attribute_guids = get_option('wc1c_guid_attributes', array());
        $terms = array();
        foreach ($product['ЗначенияСвойств'] as $property) {

            if($property['Ид'] == "first_date" && is_array($property['Значение']) && !empty($property['Значение'][0])) {
                delete_post_meta($post_id, 'planned_date');
                if($property['Значение'][0] == 'false' && isset($property['Значение'][0])){
                    update_post_meta($post_id, 'first_date', 'false');
                }else {
                    update_post_meta($post_id, 'first_date',date("d.m.Y", strtotime($property['Значение'][0])));
                }
                continue;
            }
            if($property['Ид'] == "planned_date") {
                if($property['Значение'][0] == 'false' && isset($property['Значение'][0])){
                    update_post_meta($post_id, 'planned_date', 'false');
                }else {
                    update_post_meta($post_id, 'planned_date', date("d.m.Y", strtotime($property['Значение'][0])));
                }
                continue;
            }

            if($property['Ид'] == "add_base_category") {
                if(empty($property['Значение'][0])){
                    $property['Значение'][0] = 'false';
                }
                update_post_meta($post_id, 'add_base_category', $property['Значение'][0]);
            }

            $attribute_guid = $property['Ид'];
            $attribute_id = @$attribute_guids[$attribute_guid];
            if (!$attribute_id) {
                continue;
            }

            $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
            if (!$attribute) {
                wc1c_error("Failed to get attribute");
            }

            $attribute_terms = array();
            $attribute_values = array();
            $property_values = @$property['Значение'];
            if ($property_values) {
                foreach ($property_values as $property_value) {
                    if (!$property_value) {
                        continue;
                    }

                    if ($attribute['attribute_type'] == 'select' && preg_match("/^\w+-\w+-\w+-\w+-\w+$/",
                            $property_value)) {
                        $term_id = wc1c_term_id_by_meta('wc1c_guid', "{$attribute['taxonomy']}::$property_value");
                        if ($term_id) {
                            $attribute_terms[] = (int)$term_id;
                        }
                    } else {
                        if (!defined('WC1C_MULTIPLE_VALUES_DELIMETER')) {
                            $attribute_values[] = $property_value;
                        } else {
                            $term_names = explode(WC1C_MULTIPLE_VALUES_DELIMETER, $property_value);
                            $term_names = array_map('trim', $term_names);
                            foreach ($term_names as $term_name) {
                                $result = get_term_by('name', $term_name, $attribute['taxonomy'], ARRAY_A);
                                if (!$result) {
                                    $slug = wc1c_unique_term_slug($term_name, $attribute['taxonomy']);
                                    $args = array(
                                        'slug' => $slug,
                                    );
                                    $result = wp_insert_term($term_name, $attribute['taxonomy'], $args);
                                    wc1c_check_wpdb_error();
                                    wc1c_check_wp_error($result);
                                }
                                $attribute_terms[] = $result['term_id'];
                            }
                        }
                    }
                }
            }

            if ($attribute_terms || $attribute_values) {
                $product_attribute = array(
                    'name' => null,
                    'value' => '',
                    'position' => count($product_attributes),
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );

                if ($attribute_terms) {
                    $product_attribute['name'] = $attribute['taxonomy'];
                    $product_attribute['is_taxonomy'] = 1;
                } elseif ($attribute_values) {
                    $product_attribute['name'] = $attribute['attribute_label'];
                    $product_attribute['value'] = implode(" | ", $attribute_values);
                }

                $product_attribute_key = sanitize_title($attribute['taxonomy']);
                $product_attributes[$product_attribute_key] = $product_attribute;
            }

            if ($attribute_terms) {
                if (!isset($terms[$attribute['taxonomy']])) {
                    $terms[$attribute['taxonomy']] = array();
                }
                $terms[$attribute['taxonomy']] = array_merge($terms[$attribute['taxonomy']], $attribute_terms);
            }
        }

        foreach ($terms as $attribute_taxonomy => $attribute_terms) {
            register_taxonomy($attribute_taxonomy, null);
            wc1c_update_product_category($post_id, $attribute_terms, $attribute_taxonomy);
        }
        if($product['add_base_category'] == true || $product['add_base_category'] == 'true'){
            deleteCategoryAll($post_id);
        }
    }

    foreach ($product['ЗначенияРеквизитов'] as $requisite) {
        $attribute_values = @$requisite['Значение'];
        if (!$attribute_values) {
            continue;
        }
        if (strpos($attribute_values[0], "import_files/") === 0) {
            continue;
        }

        $requisite_name = $requisite['Наименование'];
        $product_attribute_name = strpos($requisite_name, ' ') === false ? preg_replace_callback("/(?<!^)\p{Lu}/u",
            'wc1c_replace_requisite_name_callback', $requisite_name) : $requisite_name;
        $product_attribute_key = sanitize_title($requisite_name);
        $product_attribute_position = count($product_attributes);
        $product_attributes[$product_attribute_key] = array(
            'name' => wc_clean($product_attribute_name),
            'value' => implode(" | ", $attribute_values),
            'position' => $product_attribute_position,
            'is_visible' => 0,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );

    }

    foreach ($product['ХарактеристикиТовара'] as $characteristic) {
        $attribute_value = @$characteristic['Значение'];
        if (!$attribute_value) {
            continue;
        }

        $product_attribute_name = $characteristic['Наименование'];
        $product_attribute_key = sanitize_title($product_attribute_name);
        $product_attribute_position = count($product_attributes);
        $product_attributes[$product_attribute_key] = array(
            'name' => wc_clean($product_attribute_name),
            'value' => $attribute_value,
            'position' => $product_attribute_position,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
    }

    if (!in_array('attributes', $preserve_fields)) {
        $old_product_attributes = array_diff_key($current_product_attributes, $product_attributes);
        $old_taxonomies = array();
        foreach ($old_product_attributes as $old_product_attribute) {
            if ($old_product_attribute['is_taxonomy']) {
                $old_taxonomies[] = $old_product_attribute['name'];
            } else {
                $key = array_search($old_product_attribute, $product_attributes);
                if ($key !== false) {
                    unset($product_attributes[$key]);
                }
            }
        }
        foreach ($old_taxonomies as $old_taxonomy) {
            register_taxonomy($old_taxonomy, null);
        }
        wp_delete_object_term_relationships($post_id, $old_taxonomies);

        ksort($current_product_attributes);
        $product_attributes_copy = $product_attributes;
        ksort($product_attributes_copy);
        if ($current_product_attributes != $product_attributes_copy) {
            $product_attributes = array_merge($product_attributes, $current_product_attribute_variations);
            update_post_meta($post_id, '_product_attributes', $product_attributes);
        }
    }

    if (!in_array('attachments', $preserve_fields)) {
        $attachments = array();
        if (!empty($product['Картинка'])) {
            $attachments = array_filter($product['Картинка']);
            $attachments = array_fill_keys($attachments, array());
        }

        if ($product['ЗначенияРеквизитов']) {
            $attachment_keys = array(
                'ОписаниеФайла' => 'description',
            );
            foreach ($product['ЗначенияРеквизитов'] as $requisite) {
                $attribute_name = $requisite['Наименование'];
                if (!isset($attachment_keys[$attribute_name])) {
                    continue;
                }

                $attribute_values = @$requisite['Значение'];
                if (!$attribute_values) {
                    continue;
                }

                $attribute_value = $attribute_values[0];
                if (strpos($attribute_value, "import_files/") !== 0) {
                    continue;
                }

                list($picture_path, $attribute_value) = explode('#', $attribute_value, 2);
                if (!isset($attachments[$picture_path])) {
                    continue;
                }

                $attachment_key = $attachment_keys[$attribute_name];
                $attachments[$picture_path][$attachment_key] = $attribute_value;
            }
        }

        //TODO disable image load
        if (false){
            //if ($attachments) {
            $attachment_ids = wc1c_replace_post_attachments($post_id, $attachments);

            $new_post_meta = array(
                '_product_image_gallery' => implode(',', array_slice($attachment_ids, 1)),
                '_thumbnail_id' => @$attachment_ids[0],
            );
            foreach ($new_post_meta as $meta_key => $meta_value) {
                if ($meta_value != @$post_meta[$meta_key]) {
                    update_post_meta($post_id, $meta_key, $meta_value);
                }
            }
        }
    }

    do_action('wc1c_post_product', $post_id, $is_added, $product, $is_full);

    return $post_id;
}

function wc1c_replace_subproducts($is_full, $subproducts, $wc1c_ar_options)
{
    require_once sprintf(WC1C_PLUGIN_DIR . "exchange/offers.php");

    wc1c_replace_suboffers($is_full, $subproducts, true, false, array(), $wc1c_ar_options);
}

function wc1c_deactivate_product()
{
    global $wpdb;

    $product_post = get_posts(array(
        'posts_per_page' => -1,
        'offset' => 0,
        'post_type' => 'product',
        'orderby' => 'ID',
        'order' => 'asc',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_wc1c_import',
                'compare' => 'NOT EXISTS'
            ),
        )
    ));

    foreach ($product_post as $product) {
        $product_data = array(
            'ID' => $product->ID,
            'post_status' => 'draft'
        );
        wp_update_post($product_data);
    }

    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key=%s", "_wc1c_import"));
}

function wc1c_deactivate_product_on_date($date_deactivate = "")
{
    $product_post = get_posts(array(
        'posts_per_page' => -1,
        'offset' => 0,
        'post_type' => 'product',
        'orderby' => 'ID',
        'order' => 'asc',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_wc1c_timestamp',
                'value' => (int)$date_deactivate,
                'compare' => '<',
            ))
    ));


    foreach ($product_post as $product) {
        $product_data = array(
            'ID' => $product->ID,
            'post_status' => 'draft'
        );
        wp_update_post($product_data);
    }
}

function wc1c_clean_posts($post_type)
{
    global $wpdb;

    $post_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta JOIN $wpdb->posts ON post_id = ID WHERE post_type = %s AND meta_key = '_wc1c_timestamp' AND meta_value != %d",
        $post_type, WC1C_TIMESTAMP));
    wc1c_check_wpdb_error();

    foreach ($post_ids as $post_id) {
        wp_trash_post($post_id);
    }
}

function wc1c_clean_products($is_full)
{
    if (!$is_full || WC1C_PREVENT_CLEAN) {
        return;
    }

    wc1c_clean_posts('product');
}

function wc1c_clean_woocommerce_categories($is_full)
{
    global $wpdb;

    if (!$is_full || WC1C_PREVENT_CLEAN) {
        return;
    }

    $term_ids = $wpdb->get_col($wpdb->prepare("SELECT tm.term_id FROM $wpdb->termmeta tm JOIN $wpdb->term_taxonomy tt ON tm.term_id = tt.term_id WHERE taxonomy = 'product_cat' AND meta_key = 'wc1c_timestamp' AND meta_value != %d",
        WC1C_TIMESTAMP));
    wc1c_check_wpdb_error();

    $term_ids = apply_filters('wc1c_clean_categories', $term_ids);
    if (!$term_ids) {
        return;
    }

    foreach ($term_ids as $term_id) {
        $result = wp_delete_term($term_id, 'product_cat');
        wc1c_check_wp_error($result);
    }
}

function wc1c_clean_woocommerce_attributes($is_full)
{
    global $wpdb;

    if (!$is_full || WC1C_PREVENT_CLEAN) {
        return;
    }

    $timestamps = get_option('wc1c_timestamp_attributes', array());
    if (!$timestamps) {
        return;
    }

    $guids = get_option('wc1c_guid_attributes', array());

    $attribute_ids = array();
    foreach ($timestamps as $guid => $timestamp) {
        if ($timestamp != WC1C_TIMESTAMP) {
            $attribute_ids[] = $guids[$guid];
        }
    }

    $attribute_ids = apply_filters('wc1c_clean_attributes', $attribute_ids);
    if (!$attribute_ids) {
        return;
    }

    foreach ($attribute_ids as $attribute_id) {
        $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
        if (!$attribute) {
            continue;
        }

        wc1c_delete_woocommerce_attribute($attribute_id);

        unset($guids[$guid]);
        unset($timestamps[$guid]);

        $is_deleted = true;
    }

    if (!empty($is_deleted)) {
        $orders = get_option('wc1c_order_attributes', array());
        $order_index = array_search($attribute_id, $orders);
        if ($order_index !== false) {
            unset($orders[$order_index]);
            update_option('wc1c_order_attributes', $orders);
        }

        update_option('wc1c_guid_attributes', $guids);
        update_option('wc1c_timestamp_attributes', $timestamps);
    }
}

function wc1c_clean_woocommerce_attribute_options($is_full, $attribute_taxonomy)
{
    global $wpdb;

    if (!$is_full || WC1C_PREVENT_CLEAN) {
        return;
    }

    $term_ids = $wpdb->get_col($wpdb->prepare("SELECT tm.term_id FROM $wpdb->termmeta tm JOIN $wpdb->term_taxonomy tt ON tm.term_id = tt.term_id WHERE taxonomy = %s AND meta_key = 'wc1c_timestamp' AND meta_value != %d",
        $attribute_taxonomy, WC1C_TIMESTAMP));
    wc1c_check_wpdb_error();

    foreach ($term_ids as $term_id) {
        $result = wp_delete_term($term_id, $attribute_taxonomy);
        wc1c_check_wp_error($result);
    }
}

function wc1c_clean_product_terms()
{
    global $wpdb;

    $wpdb->query("UPDATE $wpdb->term_taxonomy tt SET count = (SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = tt.term_taxonomy_id) WHERE taxonomy LIKE 'pa_%'");
    wc1c_check_wpdb_error();

    $rows = $wpdb->get_results("SELECT tm.term_id, taxonomy FROM $wpdb->term_taxonomy tt LEFT JOIN $wpdb->termmeta tm ON tt.term_id = tm.term_id AND meta_key = 'wc1c_guid' WHERE meta_value IS NULL AND taxonomy LIKE 'pa_%' AND count = 0");
    wc1c_check_wpdb_error();

    foreach ($rows as $row) {
        register_taxonomy($row->taxonomy, null);
        $result = wp_delete_term($row->term_id, $row->taxonomy);
        wc1c_check_wp_error($result);
    }
}


function akimbo_wc1c_update_catalog($post_id,$is_added){
    global $wpdb;

    if ($is_added) {
        //Enterego группа по умолчанию
        $category_id = wc1c_term_id_by_meta('wc1c_guid', "product_cat::soon_sale");
        if (!empty($category_id)) {
            wc1c_update_product_category($post_id, array($category_id));
        }
    } else {
        //Enterego - признак новинка устанавливается на две недели
        $datetime_new_category = get_post_meta($post_id, 'wc1c_datetime_new_category', true);
        if (!empty($datetime_new_category)) {
            //Enterego DEBUG
            wc1c_save_error_message("tet_new",array('date'=>$datetime_new_category));

            if (time() - $datetime_new_category > 2419200) {
                $category_id = wc1c_term_id_by_meta('wc1c_guid', "product_cat::wc1c_new");

                $wpdb->query(
                    $wpdb->prepare("DELETE tr FROM $wpdb->term_relationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                      AND tt.taxonomy = 'product_cat'
                      WHERE (object_id=%d) AND tt.term_taxonomy_id=%d",$post_id, $category_id));

                $count = $wpdb->get_var(
                    $wpdb->prepare("SELECT  COUNT(tr.object_id) FROM  $wpdb->term_relНЬДationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                      WHERE  tr.term_taxonomy_id=%d GROUP BY tr.term_taxonomy_id", $category_id)
                );
                if (empty($count))
                    $count = 0;

                $wpdb->query($wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count=%d WHERE term_taxonomy_id = %d", $count, $category_id));

                delete_post_meta($post_id,'wc1c_datetime_new_category');
            }
        }
    }
}

//
//Enterego - обновление категории товара с в скоро в продаже на новинки
/**
 * @param $post_id
 * @param $id_coming_soon
 * @param $id_category_new
 * @return bool
 */
function wc1c_update_coming_soon_sale($post_id, $id_coming_soon, $id_category_new)
{
    global $wpdb;

    $post_in_coming_soon = $wpdb->get_var($wpdb->prepare("SELECT  object_id FROM  $wpdb->term_relationships as tr
        WHERE tr.object_id=%d AND tr.term_taxonomy_id=%d", $post_id, $id_coming_soon));

    if (empty($post_in_coming_soon))
        return false;

    $wpdb->query(
        $wpdb->prepare("DELETE tr FROM $wpdb->term_relationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                      AND tt.taxonomy = 'product_cat'
                      WHERE (object_id=%d) AND (tt.term_taxonomy_id=%d OR tt.term_taxonomy_id=%d)",$post_id, $id_coming_soon,$id_category_new));

    $count = $wpdb->get_var(
        $wpdb->prepare("SELECT  COUNT(tr.object_id) FROM  $wpdb->term_relationships tr
                      INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
                      WHERE  tr.term_taxonomy_id=%d GROUP BY tr.term_taxonomy_id", $id_category_new)
    );
    if (empty($count))
        $count = 0;

    $wpdb->query(
        $wpdb->prepare("INSERT $wpdb->term_relationships (object_id,term_taxonomy_id,term_order)  VALUES(%d,%d,%d)",
            $post_id, $id_category_new, 0)
    );

    $wpdb->query($wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count=%d WHERE term_taxonomy_id = %d", $count, $id_category_new));
    return true;
}