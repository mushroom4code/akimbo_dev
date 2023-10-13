<?php
function users_recently_viewed_products() {
    if (!is_product()) {
        return;
    }
    if (is_user_logged_in()) {
        if (empty(get_user_meta(get_current_user_id(), 'recently_viewed_products'))) {
            $viewed_products = array();
        } else {
            $viewed_products = (array)explode('|', get_user_meta(get_current_user_id(), 'recently_viewed_products')[0]);
        }

        if (!in_array(get_the_ID(), $viewed_products)) {
            $viewed_products[] = (string)get_the_ID();
        }

        if (sizeof($viewed_products) > 5) {
            array_shift($viewed_products);
        }
        update_user_meta(get_current_user_id(), 'recently_viewed_products', $viewed_products);
    }
}

add_action( 'template_redirect', 'users_recently_viewed_products', 20 );

function setupFieldForWatchedProducts($user_id) {
    update_user_meta($user_id, 'last_watched_produсts_date_notification', current_time('timestamp'));
}

add_action( 'user_register', 'setupFieldForWatchedProducts');

function setupFieldForEmailUserAgreement($user_id) {
    update_user_meta($user_id, 'email_agreement_field', '1');
}

add_action('user_register', 'setupFieldForEmailUserAgreement');

function user_last_login() {
    if (is_user_logged_in()) {
        update_user_meta( wp_get_current_user()->ID, 'last_login', current_time('timestamp'));
    }
}

add_action('init', 'user_last_login');

function checkForWatchedProductsReadiness()
{
    $user_query = new WP_User_Query(array('role' => 'Customer'));
    $header = "Content-Type: text/html\r\n";
    if (get_option('woocommerce_email_from_address') && get_option('woocommerce_email_from_name')) {
        $header .= 'Reply-to: ' . get_option('woocommerce_email_from_name') . ' <' . get_option('woocommerce_email_from_address') . ">\r\n";
        $header .= 'From: ' . get_option('woocommerce_email_from_name') . ' <' . get_option('woocommerce_email_from_address') . ">\r\n";
    }
    foreach ($user_query->get_results() as $user) {
        if (get_user_meta($user->ID, 'email_agreement_field')[0] === '1') {
            $lastWatchedProduсtsDateNotification = get_user_meta($user->ID, 'last_watched_produсts_date_notification');
            if (empty($lastWatchedProduсtsDateNotification)) {
                update_user_meta($user->ID, 'last_watched_produсts_date_notification', current_time('timestamp'));
                continue;
            }
            $last_login = get_user_meta($user->ID, 'last_login');
            if (!empty($last_login) && $last_login[0] - $lastWatchedProduсtsDateNotification[0] >= 86400) {
                $recently_viewed_products = get_user_meta($user->ID, 'recently_viewed_products');
                if (!empty($recently_viewed_products)) {
                    $isAnyAvailableProducts = false;
                    foreach ($recently_viewed_products[0] as $productId) {
                        $product = wc_get_product($productId);
                        if ($product->is_in_stock() && $product->get_status() === 'publish') {
                            $isAnyAvailableProducts = true;
                            break;
                        }
                    }
                    if ($isAnyAvailableProducts) {
                        ob_start();
                        include(ABSPATH . 'wp-content/themes/salient/woocommerce/emails/customer-previous-products.php');
                        $viewed_products_letter = ob_get_contents();
                        ob_end_clean();
                        if (wp_mail($user->user_email, 'AKIMBO: Просмотренные товары', $viewed_products_letter, $header)) {
                            update_user_meta($user->ID, 'last_watched_produсts_date_notification', current_time('timestamp'));
                        }
                    }
                }
            }
        }
    }
}

if ( ! wp_next_scheduled( 'checkForWatchedProductsReadinessHook' ) ) {
    wp_schedule_event( time(), 'hourly', 'checkForWatchedProductsReadinessHook' );
}