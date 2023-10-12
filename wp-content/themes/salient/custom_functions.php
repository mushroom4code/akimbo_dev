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
    update_user_meta($user_id, 'email_agreement', 'true');
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
        $email_agreement = get_user_meta($user->ID, 'email_agreement');
        if (!empty($email_agreement)) {
            if ($email_agreement[0] === 'true') {
                $lastWatchedProduсtsDateNotification = get_user_meta($user->ID, 'last_watched_produсts_date_notification');
                if ($lastWatchedProduсtsDateNotification) {
                    $last_login = get_user_meta($user->ID, 'last_login');
                    if (!empty($last_login) && $last_login[0] - $lastWatchedProduсtsDateNotification[0] >= 86400) {
                        $recently_viewed_products = get_user_meta($user->ID, 'recently_viewed_products');
                        if (!empty($recently_viewed_products)) {
                            $isAnyAvailableProducts = false;
                            foreach ($recently_viewed_products[0] as $productId) {
                                if (wc_get_product($productId)->get_stock_quantity() >= 1) {
                                    $isAnyAvailableProducts = true;
                                    break;
                                }
                            }
                            if($isAnyAvailableProducts) {
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
                } else {
                    update_user_meta($user->ID, 'last_watched_produсts_date_notification', current_time('timestamp'));
                }
            }
        } else {
            if (empty(get_user_meta($user->ID, 'email_agreement'))) {
                update_user_meta($user->ID, 'email_agreement', 'true');
            }
        }
    }
}

if ( ! wp_next_scheduled( 'checkForWatchedProductsReadinessHook' ) ) {
    wp_schedule_event( time(), 'hourly', 'checkForWatchedProductsReadinessHook' );
}

add_action( 'checkForWatchedProductsReadinessHook', 'checkForWatchedProductsReadiness' );

add_action( 'wp_ajax_viewed_products_newsletter_change', 'ViewedProductsNewsletterChange' );
function ViewedProductsNewsletterChange() {
    $status = false;
    if (isset($_POST['value'])) {
        if($_POST['value'] === 'true') {
            $status = update_user_meta(get_current_user_id(), 'email_agreement', 'true');
        } else {
            $status = update_user_meta(get_current_user_id(), 'email_agreement', 'false');
        }
    }
    exit(json_encode($status));
}