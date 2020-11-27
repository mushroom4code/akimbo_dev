<?php

// -----------------------------------------------------------------#
// Default theme constants
// -----------------------------------------------------------------#
define('NECTAR_THEME_DIRECTORY', get_template_directory());
define('NECTAR_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/nectar/');
define('NECTAR_THEME_NAME', 'salient');


if(!function_exists('get_nectar_theme_version')) {
    function nectar_get_theme_version()
    {
        return '10.0.1';
    }
}


// -----------------------------------------------------------------#
// Load text domain
// -----------------------------------------------------------------#
add_action('after_setup_theme', 'nectar_lang_setup');

if(!function_exists('nectar_lang_setup')) {
    function nectar_lang_setup()
    {

        load_theme_textdomain(NECTAR_THEME_NAME, get_template_directory() . '/lang');

    }
}


// -----------------------------------------------------------------#
// Helper to grab Salient theme options
// -----------------------------------------------------------------#
function get_nectar_theme_options()
{

    $legacy_options = get_option('salient');
    $current_options = get_option('salient_redux');

    if(!empty($current_options)) {
        return $current_options;
    } elseif(!empty($legacy_options)) {
        return $legacy_options;
    } else {
        return $current_options;
    }
}

$nectar_options = get_nectar_theme_options();
$nectar_get_template_directory_uri = get_template_directory_uri();


// Default WP video size.
$content_width = 1080;


// -----------------------------------------------------------------#
// Register/Enqueue JS
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/enqueue-scripts.php';


// -----------------------------------------------------------------#
// Register/Enqueue CSS
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/enqueue-styles.php';


// -----------------------------------------------------------------#
// Dynamic Styles
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/dynamic-styles.php';


// Dynamic CSS to be loadded in head.
$nectar_external_dynamic = (!empty($nectar_options['external-dynamic-css']) && $nectar_options['external-dynamic-css'] == 1) ? 'on' : 'off';
if($nectar_external_dynamic != 'on') {

    add_action('wp_head', 'nectar_colors_css_output');
    add_action('wp_head', 'nectar_custom_css_output');
    add_action('wp_head', 'nectar_fonts_output');

} // Dynamic CSS to be enqueued in a file.
else {
    add_action('wp_enqueue_scripts', 'nectar_enqueue_dynamic_css');
}


// -----------------------------------------------------------------#
// Category Custom Meta
// -----------------------------------------------------------------#
require 'nectar/meta/category-meta.php';


// -----------------------------------------------------------------#
// Image sizes
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/media.php';


// -----------------------------------------------------------------#
// Navigation menu locations and custom fields
// -----------------------------------------------------------------#
require_once 'nectar/assets/functions/wp-menu-custom-items/menu-item-custom-fields.php';

require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/nav-menus.php';


// -----------------------------------------------------------------#
// TGM
// -----------------------------------------------------------------#
$nectar_disable_tgm = (!empty($nectar_options['disable_tgm']) && $nectar_options['disable_tgm'] == '1') ? true : false;

if(!$nectar_disable_tgm) {
    require_once 'nectar/tgm-plugin-activation/class-tgm-plugin-activation.php';
    require_once 'nectar/tgm-plugin-activation/required_plugins.php';
}


// -----------------------------------------------------------------#
// Nectar WPBakery Page Builder
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wpbakery-init.php';


// -----------------------------------------------------------------#
// Theme Skin
// -----------------------------------------------------------------#
$nectar_theme_skin = (!empty($nectar_options['theme-skin'])) ? $nectar_options['theme-skin'] : 'original';
$nectar_header_format = (!empty($nectar_options['header_format'])) ? $nectar_options['header_format'] : 'default';

if($nectar_header_format == 'centered-menu-bottom-bar') {
    $nectar_theme_skin = 'material';
}

add_filter('body_class', 'nectar_theme_skin_class');

function nectar_theme_skin_class($classes)
{
    global $nectar_theme_skin;
    $classes[] = $nectar_theme_skin;
    return $classes;
}


function nectar_theme_skin_css()
{
    global $nectar_theme_skin;
    wp_enqueue_style('skin-' . $nectar_theme_skin);
}

add_action('wp_enqueue_scripts', 'nectar_theme_skin_css');


// -----------------------------------------------------------------#
// Search
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/search.php';


// -----------------------------------------------------------------#
// General WP
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wp-general.php';


// -----------------------------------------------------------------#
// Widget areas and custom widgets
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/widgets.php';


// -----------------------------------------------------------------#
// Header
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/header.php';


// -----------------------------------------------------------------#
// Blog
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/blog.php';


// -----------------------------------------------------------------#
// Portfolio
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/portfolio.php';


// -----------------------------------------------------------------#
// Page
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/page.php';


// -----------------------------------------------------------------#
// Options panel
// -----------------------------------------------------------------#
define('CNKT_INSTALLER_PATH', NECTAR_FRAMEWORK_DIRECTORY . 'redux-framework/extensions/wbc_importer/wbc_importer/connekt-plugin-installer/');

$using_nectar_redux_framework = false;

if(!class_exists('ReduxFramework') && file_exists(dirname(__FILE__) . '/nectar/redux-framework/ReduxCore/framework.php')) {
    require_once dirname(__FILE__) . '/nectar/redux-framework/ReduxCore/framework.php';
    $using_nectar_redux_framework = true;
}
if(!isset($redux_demo) && file_exists(dirname(__FILE__) . '/nectar/redux-framework/options-config.php')) {
    require_once dirname(__FILE__) . '/nectar/redux-framework/options-config.php';
}


require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/redux-salient.php';


// -----------------------------------------------------------------#
// Nectar love
// -----------------------------------------------------------------#
require_once 'nectar/love/nectar-love.php';


// -----------------------------------------------------------------#
// Page meta
// -----------------------------------------------------------------#
require 'nectar/meta/page-meta.php';

$nectar_disable_home_slider = (!empty($nectar_options['disable_home_slider_pt']) && $nectar_options['disable_home_slider_pt'] == '1') ? true : false;
$nectar_disable_nectar_slider = (!empty($nectar_options['disable_nectar_slider_pt']) && $nectar_options['disable_nectar_slider_pt'] == '1') ? true : false;


// -----------------------------------------------------------------#
// Home slider
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/home-slider.php';


if($nectar_disable_home_slider != true) {
    include 'nectar/meta/home-slider-meta.php';
}


// -----------------------------------------------------------------#
// Nectar Slider
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/nectar-slider.php';


if($nectar_disable_nectar_slider != true) {
    include 'nectar/meta/nectar-slider-meta.php';
}


// -----------------------------------------------------------------#
// WPML
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/wpml.php';

// -----------------------------------------------------------------#
// Gutenberg
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/gutenberg.php';


// -----------------------------------------------------------------#
// Shortcodes
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/shortcodes.php';


// -----------------------------------------------------------------#
// Portfolio Meta
// -----------------------------------------------------------------#
require 'nectar/meta/portfolio-meta.php';


// -----------------------------------------------------------------#
// Post meta
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/admin-enqueue.php';


// Post meta core functions.
require 'nectar/meta/meta-config.php';
require 'nectar/meta/post-meta.php';


// -----------------------------------------------------------------#
// Pagination
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/pagination.php';


// -----------------------------------------------------------------#
// Page header
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/page-header.php';


// -----------------------------------------------------------------#
// Woocommerce
// -----------------------------------------------------------------#
global $woocommerce;

// admin notice for left over uneeded template files.
if($woocommerce && is_admin() && file_exists(dirname(__FILE__) . '/woocommerce/cart/cart.php')) {
    include 'nectar/woo/admin-notices.php';
}

// load product quickview.
$nectar_quick_view_in_use = 'false';
if($woocommerce) {
    $nectar_quick_view = (!empty($nectar_options['product_quick_view']) && $nectar_options['product_quick_view'] == '1') ? true : false;
    if($nectar_quick_view) {
        $nectar_quick_view_in_use = 'true';
        require_once 'nectar/woo/quick-view.php';
    }
}

require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/woocommerce.php';


// -----------------------------------------------------------------#
// Open Graph
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/open-graph.php';


add_action('init', 'woocommerce_clear_cart_url');
function woocommerce_clear_cart_url()
{
    global $woocommerce;
    if(isset($_REQUEST['clear-cart'])) {
        $woocommerce->cart->empty_cart();
    }
}

function theme_js()
{
    wp_enqueue_script('theme_js', get_template_directory_uri() . '/js/ee-cart.js');
}

add_action('wp_enqueue_scripts', 'theme_js');


//// Add these new sorting arguments to the sortby options on the frontend
//function custom_woocommerce_catalog_orderby( $orderby ) {
//    unset($orderby["popularity"]);
//    return $orderby;
//}
//add_filter( "woocommerce_catalog_orderby", "custom_woocommerce_catalog_orderby", 20 );
//
//function skyverge_add_new_postmeta_orderby( $sortby ) {
//
//    // Adjust the text as desired
//    $sortby['totalsale'] = 'По популярности';
//
//
//    return $sortby;
//}
//add_filter( 'woocommerce_default_catalog_orderby_options', 'skyverge_add_new_postmeta_orderby' );
//add_filter( 'woocommerce_catalog_orderby', 'skyverge_add_new_postmeta_orderby' );


function action_woocommerce_created_customer($customer_id, $new_customer_data, $password_generated)
{
    $customer_phone = get_user_meta($customer_id, 'billing_phone', true);
    $customer_name = get_user_meta($customer_id, 'billing_first_name', true);
    $customer_mail = $new_customer_data['user_email'];
    $customer_data = array();
    $customer_data = [
        'customer_phone' => $customer_phone,
        'customer_mail' => $customer_mail,
        'customer_name' => $customer_name
    ];

    if(require_once WP_CONTENT_DIR . '/themes/salient/Sendpulse/index.php') {
        $emails = array(
            array(
                'email' => $customer_data['customer_mail'],
                'variables' => array(
                    'phone' => $customer_data['customer_phone'],
                    'имя' => $customer_data['customer_name'],
                )
            )
        );
        ee_add_email_addresses(2481237, $emails);
    }
}

// add the action
add_action('woocommerce_created_customer', 'action_woocommerce_created_customer', 10, 3);

function eeCustomVisibility($q)
{
    global $wp_query;

    $badProductsIds = [];

    $currentCatSlug = $wp_query->query['product_cat'];

    $query = new WC_Product_Query(array(
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'status' => 'publish',
        'category' => $currentCatSlug
    ));

    $productsArr = $query->get_products();

    if(!empty($productsArr) && is_array($productsArr)) {
        foreach($productsArr as $productObj) {
            $productImg = $productObj->get_image_id();
            $productPrice = $productObj->get_price();
            $productQuantity = $productObj->get_stock_quantity();
            $productIsBackOrder = $productObj->get_backorders();
            $isUpcoming = ($currentCatSlug == 'upcoming') ? true : false;
            $preOrder = ($productPrice == 0 && $productQuantity == 0 && $productIsBackOrder == 'yes') ? true : false;


            if(empty($productImg) || (
                    $productQuantity <= 0 && $productIsBackOrder !== 'yes' && !$preOrder && !$isUpcoming
                )
            ) {
                $badProductsIds[] = $productObj->get_id();
            }
        }
    }

    $q->set('post__not_in', (array)$badProductsIds);
}

add_action('woocommerce_product_query', 'eeCustomVisibility');

// Enterego (V.Mikheev) add custom field in new order email template
add_filter('woocommerce_email_order_meta_fields', 'woocommerce_email_order_meta_fields_func', 10, 3);
function woocommerce_email_order_meta_fields_func($fields, $sent_to_admin, $order)
{
    require_once(get_template_directory() . '/email_customizer.php');
}

// Enterego (V.Mikheev) last fix, added rewiews on product page and hide tab "additional_information"
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);
function woo_remove_product_tabs($tabs)
{
    unset($tabs['additional_information']);   // Remove the additional information tab
    return $tabs;
}

function skyverge_add_postmeta_ordering_args($sort_args)
{
    $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', 'first_date');
//    $orderby_value = apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
    switch($orderby_value) {
        case 'price-desc':
            $sort_args['orderby'] = 'meta_value_num';
            $sort_args['order'] = 'desc';
            $sort_args['meta_key'] = '_price';
            break;

        case 'price':
            $sort_args['orderby'] = 'meta_value_num';
            $sort_args['order'] = 'asc';
            $sort_args['meta_key'] = '_price';
            break;

        case 'first_date':
            $sort_args['orderby'] = 'meta_value_num';
            $sort_args['order'] = 'desc';
            $sort_args['meta_key'] = 'first_date';
            break;
    }

    return $sort_args;
}

add_filter('woocommerce_get_catalog_ordering_args', 'skyverge_add_postmeta_ordering_args');


// Переопределение метода woocommerce
if(!function_exists('woocommerce_catalog_ordering')) {
    /**
     * Output the product sorting options.
     */
    function woocommerce_catalog_ordering()
    {
        if(!wc_get_loop_prop('is_paginated') || !woocommerce_products_will_display()) {
            return;
        }
        $show_default_orderby = 'menu_order' === apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
        $catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
            'menu_order' => __('Default sorting', 'woocommerce'),
            'popularity' => __('Sort by popularity', 'woocommerce'),
            'rating' => __('Sort by average rating', 'woocommerce'),
            'first_date' => "По новизне",
//            'date'       => __( 'Sort by latest', 'woocommerce' ),
            'price' => __('Sort by price: low to high', 'woocommerce'),
            'price-desc' => __('Sort by price: high to low', 'woocommerce'),
        ));

        $default_orderby = wc_get_loop_prop('is_search') ? 'relevance' : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', ''));
        $default_orderby = $default_orderby == "date" ? "first_date" : $default_orderby;

        $orderby = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if(wc_get_loop_prop('is_search')) {
            $catalog_orderby_options = array_merge(array('relevance' => __('Relevance', 'woocommerce')), $catalog_orderby_options);

            unset($catalog_orderby_options['menu_order']);
        }

        if(!$show_default_orderby) {
            unset($catalog_orderby_options['menu_order']);
        }

        if('no' === get_option('woocommerce_enable_review_rating')) {
            unset($catalog_orderby_options['rating']);
        }

        if(!array_key_exists($orderby, $catalog_orderby_options)) {
            $orderby = current(array_keys($catalog_orderby_options));
        }

        wc_get_template('loop/orderby.php', array(
            'catalog_orderby_options' => $catalog_orderby_options,
            'orderby' => $orderby,
            'show_default_orderby' => $show_default_orderby,
        ));
    }
}


add_action('add_meta_boxes', 'myplugin_add_custom_box');
function myplugin_add_custom_box()
{
    add_meta_box('enterego-href-video', 'Ссылка на видео', 'add_custom_link', 'product', 'side', 'low');
}

function add_custom_link($post, $meta)
{
    $value = get_post_meta($post->ID, 'video_link', 1);
    echo '<label for="video_link">Укажите ссыклу на видео</label> ';
    echo '<input type="text" id="video-link" name="video_link"  value="' . $value . '" size="25" />';
}

add_action('save_post', 'save_custom_link');
function save_custom_link($post_id)
{
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if(!current_user_can('edit_post', $post_id))
        return;

    if(get_post_type($post_id) !== 'product') {
        return;
    }
    $player_image_id = 144540;
    $product = new WC_product($post_id);
    if(!empty($_POST['video_link'])) {

        $my_data = sanitize_text_field($_POST['video_link']);
        $attachment_ids = $product->get_gallery_image_ids();
        $image_array = implode(',', $attachment_ids);
        $new_image_array = $image_array . ',' . $player_image_id;
        update_post_meta($post_id, '_product_image_gallery', $new_image_array);
        update_post_meta($post_id, 'video_link', $my_data);

    } else {
        $attachment_ids = $product->get_gallery_image_ids();
        $last = end($attachment_ids);
        if($last == $player_image_id) {
            array_pop($attachment_ids);
            $image_array = implode(',', $attachment_ids);
            update_post_meta($post_id, '_product_image_gallery', $image_array);
        }

        update_post_meta($post_id, 'video_link', '');
    }
}

// Enterego(V.Mikheev) add custom field to checkout page 
require_once(get_template_directory() . '/add-custom-fields.php');


// Enterego( V.Mikheev) remove mandatory field "post-index" from registration form
// Делаем поля необязательными
add_filter('woocommerce_default_address_fields', 'custom_override_default_address_fields');

// Наша перехваченная функция - $fields проходит через фильтр
function custom_override_default_address_fields($address_fields)
{
    $address_fields['postcode']['required'] = false; //почтовый индекс

    return $address_fields;
}
