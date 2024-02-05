<?php
add_filter( 'wp_new_user_notification_email_admin', 'custom_wp_new_user_notification_email', 10, 3 );
function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
    $iconicRegisterSelectValue = get_user_meta($user->id, 'iconic-register-select', true);

    $wp_new_user_notification_email['message'] .= "\r\n" . sprintf( __( 'Имя: %s' ), $user->first_name ) . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Фамилия: %s' ), $user->last_name ) . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Название комании: %s' ), $user->billing_company )
        . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'ИНН: %s' ), $user->billing__inn ) . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Название магазина: %s' ), $user->shop_name )
        . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Страна / Регион: %s' ), $user->billing_country )
        . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Адрес: %s' ), $user->billing_address_1 ) . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Населенный пункт: %s' ), $user->billing_city )
        . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Область / Регион: %s' ), $user->billing_state )
        . "\r\n\r\n";
    $wp_new_user_notification_email['message'] .= sprintf( __( 'Телефон: %s' ), $user->billing_phone ) . "\r\n\r\n";
    if ($iconicRegisterSelectValue) {
        $wp_new_user_notification_email['message'] .= sprintf(__( 'Сфера продаж: %s' ),
                iconic_get_account_fields()['iconic-register-select']['options'][$iconicRegisterSelectValue])
            . "\r\n\r\n";
    }

    return $wp_new_user_notification_email;
}