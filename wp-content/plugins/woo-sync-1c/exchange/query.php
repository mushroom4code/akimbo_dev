<?php

if (!defined('ABSPATH')) exit;

if (!defined('WC1C_CURRENCY')) define('WC1C_CURRENCY', null);

WC();

function callback($buffer)
{
    return iconv("UTF-8", "WINDOWS-1251", $buffer);
}

function wc1c_export_sale_info() {
    $dom = new DomDocument('1.0');
    $dom->encoding = WC1C_XML_CHARSET;

    $cml_info = $dom->appendChild($dom->createElement('Cтатусы'));

    $ar_list_status = wc_get_order_statuses();
    foreach ($ar_list_status as $status_key => $status_value) {
        $cml_status = $cml_info->appendChild($dom->createElement('Элемент'));
        wc1c_cmlimportAddChildXML($dom, $cml_status, "Название", $status_value);
        wc1c_cmlimportAddChildXML($dom, $cml_status, "Ид", $status_key);
    }

    print $dom->saveXML();
}

function wc1c_export_orders()
{
    if (isset($_SESSION['lost_order_offset'])) {
        $lost_order_offset = $_SESSION['lost_order_offset'];
    } else {
        $lost_order_offset = 0;
    }
    $wc1c_options = wc1c_options::wc1c_get_options();

    $order_statuses = array_keys(wc_get_order_statuses());
    $order_posts = get_posts(array(
        'posts_per_page' => 10,
        'offset' => $lost_order_offset,
        'post_type' => 'shop_order',
        'orderby' => 'ID',
        'order' => 'asc',
        'post_status' => $order_statuses,
        'date_query' => array(
            array(
                'column' => 'post_modified',
                'after' => $wc1c_options['wc1c_last_order_date_commited'],
            ),
        )
    ));

    $order_post_ids = array();
    $documents = array();
    foreach ($order_posts as $order_post) {
        $order = wc_get_order($order_post);
        if (!$order) wc1c_error("Failed to get order");

        $order_post_ids[] = $order_post->ID;

        $order_line_items = $order->get_items();

        foreach ($order_line_items as $key => $order_line_item) {
            $product_id = $order_line_item['product_id'];
            $variation_id = $order_line_item["variation_id"];

            $guid = "";
            if ($product_id) {
                $guid = get_post_meta($product_id, '_wc1c_guid', true);
                if (empty($guid)){
                    update_post_meta($product_id,'_wc1c_guid',$product_id);
                    $guid = $product_id;
                }
                if (!empty($variation_id)) {
                    $variation_guid = get_post_meta($variation_id, '_wc1c_guid', true);
                    if (empty($variation_guid)) {
                        $variation_guid = $guid . '#' . $variation_id;
                        update_post_meta($variation_id,'_wc1c_guid',$variation_guid);
                    }
                    $guid = $variation_guid;
                }
            }

            $order_line_items[$key]['wc1c_guid'] = $guid;
        }

        $order_shipping_items = $order->get_shipping_methods();

        $order_meta = get_post_meta($order_post->ID, null, true);
        foreach ($order_meta as $meta_key => $meta_value) {
            $order_meta[$meta_key] = $meta_value[0];
        }

        $address_items = array(
            'postcode' => "Почтовый индекс",
            'country_name' => "Страна",
            'state' => "Регион",
            'city' => "Город",
        );
        $contact_items = array(
            'email' => "Электронная почта",
            'phone' => "ТелефонРабочий",
        );

        $customer_id = get_post_meta($order_post->ID, '_customer_user', true);
        $customer = array();
        $customer['address'] = array();$customer['contacts'] = array();
        if (empty($customer_id)) {
            $is_guest = true;
            $customer['user_id'] = 0;
        } else {
            $is_guest = false;
            $customer['user_id'] = $customer_id;
        }

        foreach (array('billing', 'shipping') as $type) {

            $name = array();
            foreach (array('first_name', 'last_name') as $name_key) {
                $meta_key = "_{$type}_$name_key";
                if (empty($order_meta[$meta_key])) continue;

                $name[] = $order_meta[$meta_key];
                if (empty($customer[$name_key] ))
                    $customer[$name_key] = $order_meta[$meta_key];
            }

            if (empty($customer['name'])) {
                $name = implode(' ', $name);
                if ($is_guest) {
                    $customer['name'] = "Гость";
                } else {
                    $customer['name'] = $name;
                }
            }

            if (!empty($order_meta["_{$type}_country"])) {
                $country_code = $order_meta["_{$type}_country"];
                $order_meta["_{$type}_country_name"] = WC()->countries->countries[$country_code];
            }

            $full_address = array();
            foreach (array('postcode', 'country_name', 'state', 'city', 'address_1', 'address_2') as $address_key) {
                $meta_key = "_{$type}_$address_key";
                if (!empty($order_meta[$meta_key])) $full_address[] = $order_meta[$meta_key];
            }
            if (empty($customer['full_address']))
                $customer['full_address'] = implode(", ", $full_address);

            foreach ($address_items as $address_key => $address_item_name) {
                if (empty($order_meta["_{$type}_$address_key"])) continue;

                if (empty($customer['address'][$address_item_name]))
                    $customer['address'][$address_item_name] = $order_meta["_{$type}_$address_key"];
            }

            foreach ($contact_items as $contact_key => $contact_item_name) {
                if (empty($order_meta["_{$type}_$contact_key"])) continue;

                if (empty($customer['contacts'][$contact_item_name]))
                    $customer['contacts'][$contact_item_name] = $order_meta["_{$type}_$contact_key"];
            }

        }

        $products = array();
        foreach ($order_line_items as $order_line_item) {
            $products[] = array(
                'id' => $order_line_item['product_id'],
                'guid' => $order_line_item['wc1c_guid'],
                'name' => $order_line_item['name'],
                'price_per_item' => $order_line_item['line_total'] / $order_line_item['qty'],
                'quantity' => $order_line_item['qty'],
                'total' => $order_line_item['line_total'],
                'type' => "Товар",
            );
        }

        foreach ($order_shipping_items as $order_shipping_item) {
            if (!$order_shipping_item['cost']) continue;

            $products[] = array(
                'name' => $order_shipping_item['name'],
                'guid' => 'delivery',
                'price_per_item' => $order_shipping_item['cost'],
                'quantity' => 1,
                'total' => $order_shipping_item['cost'],
                'type' => "Услуга",
            );
        }

        $order_status_name = $order->get_status();

        if (WC1C_CURRENCY) $document_currency = WC1C_CURRENCY;
        else $document_currency = get_option('wc1c_currency', @$order_meta['_order_currency']);

        $document = array(
            'order_id' => $order_post->ID,
            'currency' => $document_currency,
            'total' => @$order_meta['_order_total'],
            'comment' => $order_post->post_excerpt,
            'customer' => $customer,
            'products' => $products,
            'payment_method_title' => @$order_meta['_payment_method_title'],
            'status' => $order->get_status(),
            'status_name' => $order_status_name,
            'has_shipping' => count($order_shipping_items) > 0,
            'modified_at' => $order_post->post_modified,
        );
        list($document['date'], $document['time']) = explode(' ', $order_post->post_date, 2);

        $documents[] = $document;
    }

    $documents = apply_filters('wc1c_query_documents', $documents);
    if (WC1C_XML_CHARSET != mb_internal_encoding()) {
        ob_start("callback");
    }

    $dom = new DomDocument('1.0');
    $dom->encoding = WC1C_XML_CHARSET;

    $cml = $dom->appendChild($dom->createElement('КоммерческаяИнформация'));
    wc1c_cmlimportAddAttributeXML($dom, $cml, "ВерсияСхемы", "2.08");
    wc1c_cmlimportAddAttributeXML($dom, $cml, "ДатаФормирования", date("Y-m-dTH:i:s", WC1C_TIMESTAMP));
    if (!empty($documents)) {

        //TODO options standart exchanges
        $old_version = true;
        if ($old_version)
            $cml_con = $cml;
        else
            $cml_con = $cml->appendChild($dom->createElement('Контейнер'));

        foreach ($documents as $document) {
            $document_cml = $cml_con->appendChild($dom->createElement('Документ'));
            $customer = $document['customer'];

            wc1c_cmlimportAddChildXML($dom, $document_cml, "Ид", $document['order_id']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Номер", $document['order_id']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "НомерВерсии", $document['modified_at']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Дата", $document['date']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Время", $document['time']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "ХозОперация", "Заказ товара");
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Роль", "Продавец");
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Валюта", $document['currency']);
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Сумма", $document['total']);
            //Enterego - информация о контрагенте в комментарии
            //wc1c_cmlimportAddChildXML($dom, $document_cml, "Комментарий", $document['comment']);
            $comment = "";
            //

            $cml_clients = $document_cml->appendChild($dom->createElement('Контрагенты'));

            $contragent_cml = $cml_clients->appendChild($dom->createElement('Контрагент'));
            wc1c_cmlimportAddChildXML($dom, $contragent_cml, "Ид", $customer['user_id']);
            wc1c_cmlimportAddChildXML($dom, $contragent_cml, "Роль", $type == 'billing' ? "Плательщик" : "Получатель");

            if (!empty($customer['name'])) {
                //Enterego - информация о контрагенте в комментарии
                $comment .= "Контрагент: ".$customer['name'].".";
                //
                wc1c_cmlimportAddChildXML($dom, $contragent_cml, "Наименование", $customer['name']);
                wc1c_cmlimportAddChildXML($dom, $contragent_cml, "ПолноеНаименование", $customer['name']);
            }
            if (!empty($customer['first_name'])) {
                wc1c_cmlimportAddChildXML($dom, $contragent_cml, "Имя", $customer['first_name']);
            }
            if (!empty($customer['last_name'])) {
                wc1c_cmlimportAddChildXML($dom, $contragent_cml, "Фамилия", $customer['last_name']);
            }
            if (!empty($customer['full_address']) || !empty($customer['address'])) {
                wc1c_cmlimportAddChildXML($dom, $document_cml, "АдресДоставки", $customer['full_address']);

                $address_cml = $contragent_cml->appendChild($dom->createElement('Адрес'));
                //Enterego - full address (корректное представление)
                $full_address = '';
                if (!empty($customer['full_address'])) {
                    $full_address = $customer['full_address'];
                }
                foreach ($customer['address'] as $address_item_name => $address_item_value) {
                    $addr_cml = $address_cml->appendChild($dom->createElement('АдресноеПоле'));
                    wc1c_cmlimportAddChildXML($dom, $addr_cml, "Тип", $address_item_name);
                    wc1c_cmlimportAddChildXML($dom, $addr_cml, "Значение", $address_item_value);
                    $full_address .= ", $address_item_value";
                }
                wc1c_cmlimportAddChildXML($dom, $address_cml, "Представление", $full_address);
                //
                //Enterego - информация о контрагенте в комментарии
                $comment .= "Адрес доставки: ".$full_address.".";
                //
            }
            if (count($customer['contacts'])) {
                $conts_cml = $contragent_cml->appendChild($dom->createElement('Контакты'));

                foreach ($customer['contacts'] as $contact_item_name => $contact_item_value) {
                    //Enterego
                    if ($contact_item_name == "Электронная почта") {
                        //Enterego - информация о контрагенте в комментарии
                        $comment .= "Электронная почта: $contact_item_value.";
                        //
                        $cml_phone = $contragent_cml->appendChild($dom->createElement('email'));
                        wc1c_cmlimportAddChildXML($dom, $cml_phone, "Представление", $contact_item_value);
                    }

                    if ($contact_item_name == "ТелефонРабочий") {
                        //Enterego - информация о контрагенте в комментарии
                        $comment .= "Телефон: $contact_item_value.";
                        //
                        $cml_email = $contragent_cml->appendChild($dom->createElement('Телефон'));
                        wc1c_cmlimportAddChildXML($dom, $cml_email, "Представление", $contact_item_value);
                    }
                    //
                    $cont_cml = $conts_cml->appendChild($dom->createElement('Контакт'));
                    wc1c_cmlimportAddChildXML($dom, $cont_cml, "КонтактВид", $contact_item_name);
                    wc1c_cmlimportAddChildXML($dom, $cont_cml, "Тип", $contact_item_name);
                    wc1c_cmlimportAddChildXML($dom, $cont_cml, "Значение", $contact_item_value);
                }
            }
            //Enterego - информация о контрагенте в комментарии
            $comment .= $document['comment'];
            wc1c_cmlimportAddChildXML($dom, $document_cml, "Комментарий", $comment);
            //
            $preds_cml = $contragent_cml->appendChild($dom->createElement('Представители'));
            $pred_cml = $preds_cml->appendChild($dom->createElement('Представитель'));
            $contrag_cml = $pred_cml->appendChild($dom->createElement('Контрагент'));
            wc1c_cmlimportAddChildXML($dom, $contrag_cml, "Отношение", "Контактное лицо");
            wc1c_cmlimportAddChildXML($dom, $contrag_cml, "Ид", $customer['user_id']);
            wc1c_cmlimportAddChildXML($dom, $contrag_cml, "Наименование", $customer['name']);

            wc1c_export_product_orders($document,$dom,$document_cml);

            $cml_values_requisites = $document_cml->appendChild($dom->createElement('ЗначенияРеквизитов'));
            if ($document['payment_method_title']) {
                $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
                wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Метод оплаты");
                wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", $document['payment_method_title']);
            }
            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Заказ оплачен");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", !in_array($document['status'], array('on-hold', 'pending')) ? 'true' : 'false');

            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Доставка разрешена");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", $document['has_shipping'] ? 'true' : 'false');

            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Отменен");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", $document['status'] == 'cancelled' ? 'true' : 'false');

            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Финальный статус");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", !in_array($document['status'], array('trash', 'on-hold', 'pending', 'processing')) ? 'true' : 'false');

            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Статус заказа ИД");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", "wc-".$document['status_name']);

            $cml_values_requisite = $cml_values_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Наименование", "Дата изменения статуса");
            wc1c_cmlimportAddAttributeXML($dom, $cml_values_requisite, "Значение", $document['modified_at']);

            update_post_meta($document['order_id'], 'wc1c_querying', 1);
            $lost_order_offset++;
        }
    }

    $_SESSION['lost_order_offset'] = $lost_order_offset;

    print $dom->saveXML();
    if (WC1C_XML_CHARSET != mb_internal_encoding()) {
        ob_end_flush();
    }
}

/**
 * @param array $document
 * @param DOMDocument $dom
 * @param DOMNode $document_cml
 */
function wc1c_export_product_orders($document,$dom,$document_cml){
    $cml_products = $document_cml->appendChild($dom->createElement('Товары'));

    foreach ($document['products'] as $product) {
        if (!empty($product['guid'])) {
            $product_cml = $cml_products->appendChild($dom->createElement('Товар'));

            if (empty($product['guid'])) {
                wc1c_cmlimportAddChildXML($dom, $product_cml, "Ид", 'delete');
                wc1c_cmlimportAddChildXML($dom, $product_cml, "Наименование", 'Товар удален');
            } else {
                wc1c_cmlimportAddChildXML($dom, $product_cml, "Ид", $product['guid']);
                wc1c_cmlimportAddChildXML($dom, $product_cml, "Наименование", $product['name']);
            }
            $bas = $product_cml->appendChild($dom->createElement('Единица'));
            wc1c_cmlimportAddChildXML($dom, $bas, "Код", "796");
            wc1c_cmlimportAddChildXML($dom, $bas, "НаименованиеПолное", "Штука");
            //wc1c_cmlimportAddAttributeXML($dom, $bas, "Код", "796");
            //wc1c_cmlimportAddAttributeXML($dom, $bas, "НаименованиеПолное", "Штука");

            //wc1c_cmlimportAddAttributeXML($dom, $bas, "МеждународноеСокращение", "PCE");
            wc1c_cmlimportAddChildXML($dom, $product_cml, "ЦенаЗаЕдиницу", $product['price_per_item']);
            wc1c_cmlimportAddChildXML($dom, $product_cml, "Количество", $product['quantity']);
            wc1c_cmlimportAddChildXML($dom, $product_cml, "Сумма", $product['total']);

            $cml_requisites = $product_cml->appendChild($dom->createElement('ЗначенияРеквизитов'));
            $cml_requisite = $cml_requisites->appendChild($dom->createElement('ЗначениеРеквизита'));

            wc1c_cmlimportAddAttributeXML($dom, $cml_requisite, "Наименование", "ТипНоменклатуры");
            wc1c_cmlimportAddAttributeXML($dom, $cml_requisite, "Значение", $product['type']);
        }
    }
}

function wc1c_succes_exchange(){
    $wc1c_ar_options = get_option('wc1c_options');
    if (isset($_SESSION['begin_import_order_date']))
        $wc1c_ar_options['wc1c_last_order_date_commited']=$_SESSION['begin_import_order_date'];
    else
        $wc1c_ar_options['wc1c_last_order_date_commited']=current_time( 'mysql' );
    update_option("wc1c_options",$wc1c_ar_options);

}

?>