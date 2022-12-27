<?php
add_filter( 'woocommerce_checkout_fields' , 'woocommerce_checkout_field_editor' );

function woocommerce_checkout_field_editor( $fields )
{   
    $fields['billing']['billing__inn'] = array(
        'label' => __('ИНН (ООО/ИП)'),
        'required' => true,
        'priority' => 31
    );
    $fields['billing']['billing__ogrn-bank'] = array(
        'label' => __('ОГРН и Банковские реквизиты (для юр.лиц)'),
        'required' => false,
        'priority' => 120
    );
    $fields['shipping']['shipping__transport-company'] = array(
        'label' => __('Транспортная компания'),
        'required' => false,
        'priority' => 90
    );
    $fields['shipping']['shipping__vid-transporta'] = array(
        'label' => __('Вид транспорта'),
        'required' => false,
        'priority' => 100
    );
    $fields['shipping']['shipping__fio-gruzopoluchatelya'] = array(
        'label' => __('ФИО грузополучателя'),
        'required' => false,
        'priority' => 110
    );
    $fields['shipping']['shipping__phone-gruzopoluchatelya'] = array(
        'label' => __('Контактный телефон получателя'),
        'required' => false,
        'priority' => 120
    );
    return $fields;

}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'add_custom_shipping_fields', 10, 1 );
function add_custom_shipping_fields($order){
    global $post_id;
    $order = new WC_Order( $post_id );
    $transport_company =  get_post_meta($order->get_id(), '_shipping__transport-company', true );
    $vid_transporta =  get_post_meta($order->get_id(), '_shipping__vid-transporta', true );
    $fio = get_post_meta($order->get_id(), '_shipping__fio-gruzopoluchatelya', true );
    $phone_number = get_post_meta($order->get_id(), '_shipping__phone-gruzopoluchatelya', true );
    if(!empty($transport_company)){
        echo ' <span><strong>Транспортная компания:</strong> ' . $transport_company. '</span><br/>';
    }
    if(!empty($vid_transporta)){
        echo  '<span><strong>Вид транспорта:</strong> ' . $vid_transporta . '</span><br/>';
    }
    if(!empty($fio)){
        echo  '<span><strong>ФИО грузополучателя:</strong> ' . $fio . '</span><br/>';
    }
    if(!empty($phone_number)){
        echo  '<span><strong>Контактный телефон получателя:</strong> ' . $phone_number . '</span>';
    }

}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_custom_billing_fields', 10, 1 );
function add_custom_billing_fields($order){
    global $post_id;
    $order = new WC_Order( $post_id );
    $inn =  get_post_meta($order->get_id(), '_billing__inn', true );
    $bank =  get_post_meta($order->get_id(), '_billing__ogrn-bank', true );

    if(!empty($inn)){
        echo ' <span><strong>ИНН (для ИП и юр.лиц):</strong> ' . $inn. '</span><br/>';
    }
    if(!empty($bank)){
        echo  '<span><strong>ОГРН и Банковские реквизиты (для юр.лиц):</strong> ' . $bank . '</span>';
    }


}