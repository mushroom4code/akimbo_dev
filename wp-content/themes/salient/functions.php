<?php

// -----------------------------------------------------------------#
// Default theme constants
// -----------------------------------------------------------------#
use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore;
define('NECTAR_THEME_DIRECTORY', get_template_directory());
define('NECTAR_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/nectar/');
define('NECTAR_THEME_NAME', 'salient');

require_once 'includes/ajax.php';

if (is_user_logged_in()) {
    Wholesale::require_files();
    $parser = new WS_Shortcode_parser('get_table');
    $settings = $parser->settings;
    $settings['ajax_url'] = admin_url('admin-ajax.php');
    wp_register_script('wholesale', '/wp-content/plugins/wholesale/assets/js/wholesale.js', array('jquery'));
    wp_localize_script('wholesale', 'wholesale_settings', $settings);
    wp_enqueue_script('wholesale', '/wp-content/plugins/wholesale/assets/js/wholesale.js', array('jquery'));
}

if (!function_exists('get_nectar_theme_version')) {
    function nectar_get_theme_version()
    {
        return '10.0.1';
    }
}

// -----------------------------------------------------------------#
// Load text domain
// -----------------------------------------------------------------#
add_action('after_setup_theme', 'nectar_lang_setup');

if (!function_exists('nectar_lang_setup')) {
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

    if (!empty($current_options)) {
        return $current_options;
    } elseif (!empty($legacy_options)) {
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
if ($nectar_external_dynamic != 'on') {

    add_action('wp_head', 'nectar_colors_css_output');
    add_action('wp_head', 'nectar_custom_css_output');
    add_action('wp_head', 'nectar_fonts_output');
    add_action('wp_head', 'script_js');

} // Dynamic CSS to be enqueued in a file.
else {
    add_action('wp_enqueue_scripts', 'nectar_enqueue_dynamic_css');
}

add_filter('wcb2b_login_message', 'fn_wcb2b_login_message');
function fn_wcb2b_login_message($message)
{
    return __('Войдите, чтобы узнать оптовую цену', 'woocommerce-b2b');
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

if (!$nectar_disable_tgm) {
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

if ($nectar_header_format == 'centered-menu-bottom-bar') {
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

if (!class_exists('ReduxFramework') && file_exists(dirname(__FILE__) . '/nectar/redux-framework/ReduxCore/framework.php')) {
    require_once dirname(__FILE__) . '/nectar/redux-framework/ReduxCore/framework.php';
    $using_nectar_redux_framework = true;
}
if (!isset($redux_demo) && file_exists(dirname(__FILE__) . '/nectar/redux-framework/options-config.php')) {
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


if ($nectar_disable_home_slider != true) {
    include 'nectar/meta/home-slider-meta.php';
}


// -----------------------------------------------------------------#
// Nectar Slider
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/nectar-slider.php';


if ($nectar_disable_nectar_slider != true) {
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
if ($woocommerce && is_admin() && file_exists(dirname(__FILE__) . '/woocommerce/cart/cart.php')) {
    include 'nectar/woo/admin-notices.php';
}

// load product quickview.
$nectar_quick_view_in_use = 'false';
if ($woocommerce) {
    $nectar_quick_view = (!empty($nectar_options['product_quick_view']) && $nectar_options['product_quick_view'] == '1') ? true : false;
    if ($nectar_quick_view) {
        $nectar_quick_view_in_use = 'true';
        require_once 'nectar/woo/quick-view.php';
    }
}

require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/woocommerce.php';


// -----------------------------------------------------------------#
// Open Graph
// -----------------------------------------------------------------#
require_once NECTAR_THEME_DIRECTORY . '/nectar/helpers/open-graph.php';


function theme_js()
{
    wp_enqueue_script('theme_js', get_template_directory_uri() . '/js/ee-cart.js');
}

add_action('wp_enqueue_scripts', 'theme_js');

function script_js()
{
    wp_enqueue_script('script_js', get_template_directory_uri() . '/js/script.js');
}

add_action('wp_enqueue_scripts', 'script_js');
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

    if (require_once WP_CONTENT_DIR . '/themes/salient/Sendpulse/index.php') {
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

    if (!empty($productsArr) && is_array($productsArr)) {
        foreach ($productsArr as $productObj) {
            $productImg = $productObj->get_image_id();
            $productPrice = $productObj->get_price();
            $productQuantity = $productObj->get_stock_quantity();
            $productIsBackOrder = $productObj->get_backorders();
            $isUpcoming = ($currentCatSlug == 'upcoming') ? true : false;
            $preOrder = ($productPrice == 0 && $productQuantity == 0 && $productIsBackOrder == 'yes') ? true : false;


            if (empty($productImg) || (
                    $productQuantity <= 0 && $productIsBackOrder !== 'yes' && !$preOrder && !$isUpcoming
                ) || ($productPrice <= 0 && $planned_date == 'false')
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

add_filter('woocommerce_product_tabs', 'woo_custom_title_tabs', 98);
function woo_custom_title_tabs($tabs)
{

    $tabs['reviews']['title'] = 'Показать отзывы';
    return $tabs;
}


function skyverge_add_postmeta_ordering_args($sort_args)
{
    $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
//    $orderby_value = apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
    switch ($orderby_value) {
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

function orderby_first_date($query)
{
    $ff = strpos($query, 'first_date');
    if ($ff !== false) $query = str_replace('wp_posts.menu_order,', '', $query);

    return $query;
}

add_filter('query', 'orderby_first_date');

// Переопределение метода woocommerce
if (!function_exists('woocommerce_catalog_ordering')) {
    /**
     * Output the product sorting options.
     */
    function woocommerce_catalog_ordering()
    {
        if (!wc_get_loop_prop('is_paginated') || !woocommerce_products_will_display()) {
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

        if (wc_get_loop_prop('is_search')) {
            $catalog_orderby_options = array_merge(array('relevance' => __('Relevance', 'woocommerce')), $catalog_orderby_options);

            unset($catalog_orderby_options['menu_order']);
        }

        if (!$show_default_orderby) {
            unset($catalog_orderby_options['menu_order']);
        }

        if ('no' === get_option('woocommerce_enable_review_rating')) {
            unset($catalog_orderby_options['rating']);
        }

        if (!array_key_exists($orderby, $catalog_orderby_options)) {
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
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    if (get_post_type($post_id) !== 'product') {
        return;
    }
    $player_image_id = 144540;
    $product = new WC_product($post_id);
    if (!empty($_POST['video_link'])) {

        $my_data = sanitize_text_field($_POST['video_link']);
        $attachment_ids = $product->get_gallery_image_ids();
        $image_array = implode(',', $attachment_ids);
        $new_image_array = $image_array . ',' . $player_image_id;
        update_post_meta($post_id, '_product_image_gallery', $new_image_array);
        update_post_meta($post_id, 'video_link', $my_data);

    } else {
        $attachment_ids = $product->get_gallery_image_ids();
        $last = end($attachment_ids);
        if ($last == $player_image_id) {
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
    $address_fields['company']['required'] = true; //000018603

    return $address_fields;
}

function getSale($product)
{
    $post_id = $product->get_id();
    return get_post_meta($post_id, '_new_sale_price', true);
}

function salePrice($product)
{
    $result = getSale($product);
    if ($result === '' && $result == 0 || $result === '0') {
        $text = '';
    } else {
        $text = "Ваша скидка на товар составляет  <b  class='onsale' style='color: #af8a6e;'>$result %</b>";
    }
    return $text;
}

function sale($product)
{
    $result = getSale($product);
    if ($result === '' && $result == 0 || $result === '0') {
        $text = '';
    } else {
        $text = "<b> - $result %</b>";
    }
    return $text;
}

function new_wp_text_input($field)
{
    /**
     * Output a text input box.
     *
     * @param array $field
     */

    global $thepostid, $post;

    $thepostid = empty($thepostid) ? $post->ID : $thepostid;
    $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
    $field['class'] = isset($field['class']) ? $field['class'] : 'short';
    $field['style'] = isset($field['style']) ? $field['style'] : '';
    $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
    $field['value'] = isset($field['value']) ? $field['value'] : get_post_meta($thepostid, $field['id'], true);
    $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
    $field['type'] = isset($field['type']) ? $field['type'] : 'text';
    $field['desc_tip'] = isset($field['desc_tip']) ? $field['desc_tip'] : false;
    $data_type = empty($field['data_type']) ? '' : $field['data_type'];
    if ($field['value'] !== '') {
        switch ($data_type) {
            case 'price':
                $field['class'] .= ' wc_input_price';
                $field['value'] = wc_format_localized_price($field['value']);
                break;
            case 'decimal':
                $field['class'] .= ' wc_input_decimal';
                $field['value'] = wc_format_localized_decimal($field['value']);
                break;
            case 'stock':
                $field['class'] .= ' wc_input_stock';
                $field['value'] = wc_stock_amount($field['value']);
                break;
            case 'url':
                $field['class'] .= ' wc_input_url';
                $field['value'] = esc_url($field['value']);
                break;

            default:
                break;
        }

        // Custom attribute handling
        $custom_attributes = array();

        if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {

            foreach ($field['custom_attributes'] as $attribute => $value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
            }
        }

        echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '">
		<label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label>';

        if (!empty($field['description']) && false !== $field['desc_tip']) {
            echo wc_help_tip($field['description']);
        }

        echo '<input type="' . esc_attr($field['type']) . '" class="' . esc_attr($field['class']) . '" style="' . esc_attr($field['style']) . '" name="' . esc_attr($field['name']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($field['value']) . '" placeholder="' . esc_attr($field['placeholder']) . '" ' . implode(' ', $custom_attributes) . ' /> ';

        if (!empty($field['description']) && false === $field['desc_tip']) {
            echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';
        }

        echo '</p>';
    }
}


//Периодически исчезают товары из корзины - метод is_purchasable возвращает false
//скорей всего из-за обмена с 1с
add_filter('woocommerce_variation_is_purchasable', '__return_true');

function get_quantity($id)
{
    $quantity = get_post_meta($id, '_stock')[0];
    $backorders_count = get_post_meta($id, '_backorders_count')[0];
    return ($quantity - $backorders_count);
}

function is_available_in_stock($id)
{
    if (get_quantity($id) <= 0)
        return false;
    return true;
}

function get_backorders_quantity($id)
{
    return get_post_meta($id, '_backorders_count')[0];
}

//--

#region #Products columns

// Add product new column in administration
add_filter('manage_edit-product_columns', 'woo_product_weight_column', 20);
function woo_product_weight_column($columns)
{

    $columns['actual'] = __('На складе', 'woocommerce');
    $columns['backorders'] = __('Ожидается', 'woocommerce');
    return $columns;

}

// Populate weight column
add_action('manage_product_posts_custom_column', 'woo_product_weight_column_data', 2);
function woo_product_weight_column_data($column)
{
    global $post;

    if ($column == 'actual') {
        $product = wc_get_product($post->ID);
        $stock_html = '';
        if ($product->managing_stock()) {
            $stock_html = wc_stock_amount($product->get_stock_quantity() - get_backorders_quantity($post->ID));
        }
        print $stock_html;
    } elseif ($column == 'backorders') {
        $product = wc_get_product($post->ID);
        $stock_html = '';
        if ($product->managing_stock()) {
            $stock_html = wc_stock_amount(get_backorders_quantity($post->ID));
        }

        echo wp_kses_post(apply_filters('woocommerce_admin_stock_html', $stock_html, $product));
    }
}

function get_children_data($id)
{
    return get_post_meta($id);
}

#endregion

#region Product_item

add_action('woocommerce_product_options_stock_fields', 'shop_add_custom_fields');
//if (!function_exists('art_woo_add_custom_fields')) {
function shop_add_custom_fields()
{
    global $post;
    echo '<div class="options_group">';// Группировка полей

    woocommerce_wp_text_input(
        array(
            'id' => '_backorders_count',
            'value' => wc_stock_amount(get_backorders_quantity($post->ID)),
            'label' => __('Плановое количество', 'woocommerce'),
            'desc_tip' => true,
            'description' => __('', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
            ),
            'data_type' => 'backorders',
        )
    );

    echo '</div>';
}
//}

#endregion

// правильный подстчет доступных в категории товаров #000017962
add_filter('woocommerce_subcategory_count_html', 'change_prods_count', 10, 2);
function change_prods_count($html, $category)
{
    // получаем количество опубликованных товаров в наличии
    $args = array(
        'stock_status' => 'instock',
        'status' => 'publish',
        'category' => $category->slug,
        'return' => 'ids',
        'limit' => -1,
    );
    $products = wc_get_products($args);
    return '<mark class="count">(' . esc_html(count($products)) . ')</mark>';
}

// #000018198
add_filter('yfym_variable_price_filter', 'change_regular_price_on_base', 10, 5);
function change_regular_price_on_base($price_yml, $product, $offer, $offer_id, $numFeed)
{
    $base_price = get_post_meta($offer_id, '_base_price', true);
    if ($base_price)
        $price_yml = $base_price;
    return $price_yml;
}

// #000018449
/** Replace 'An account is already registered with your email address. Please log in.' **/
add_filter( 'woocommerce_registration_error_email_exists', function($er) {
    return 'Учетная запись с вашим адресом электронной почты уже зарегистрирована.';
} );
// #000018514
add_filter( 'big_image_size_threshold', '__return_false' );

// #000018603
add_filter( 'woocommerce_checkout_fields', 'move_to_group', 10000 );
function move_to_group( $array ){
    $array['billing']['shop_name'] = array(
        'type'                 => 'text',
        'label'                => __( 'Название магазина', 'iconic' ),
        'hide_in_account'      => false,
        'hide_in_admin'        => false,
        'hide_in_checkout'     => false,
        'hide_in_registration' => false,
        'required'             => true,
        'priority'             => 35,
    );

    $array['billing']['billing_company']['label'] = __( 'Название компании (ООО/ИП)', 'iconic' );
    $array['billing']['billing_address_1']['label'] = __( 'Адрес (Юр. лицо/ИП)', 'iconic' );

    return $array;
}

add_action( 'woocommerce_created_customer', 'save_fields', 25 );
function save_fields( $user_id ) {
    if ( isset( $_POST[ 'shop_name' ] ) ) {
        update_user_meta( $user_id, 'shop_name', sanitize_text_field( $_POST['shop_name'] ) );
    }
}

wp_register_script( 'imask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', null, null, true );
wp_enqueue_script('imask');

$__user_pass = '';

add_filter( 'alg_wc_ev_email_content', 'notification_email', 10, 3 );
function notification_email($email_data, $args)
{
    global $__user_pass;
    $email_text = '';

    if ($args['context'] === 'activation_email_separate') {
        $email_data_custom = '<p>Уважаемый(ая) покупатель.</p>';
        $email_data_custom .= '<p>Пожалуйста <a href="%verification_url%" target="_blank">нажмите здесь</a>, чтобы активировать аккаунт и подтвердить свой адрес электронной почты.</p>';
        $email_data_custom .= "<br><p>Ваш пароль от личного кабинета: $__user_pass <br>Сохраните его и используйте для входа в личный кабинет ПОСЛЕ АКТИВАЦИИ.</p>";
    } elseif ($args['context'] === 'confirmation_email') {
        $email_data_custom = '<p>Ваша учетная запись была успешно активирована.</p><br>';
        $email_data_custom .= '<p>Рады что вы с нами!</p>';
    } else {
        $email_data_custom = $email_data;
    }

    $email_text .= $email_data_custom;

    return $email_text;
}

// берем пароль из формы регистрации
add_action( 'user_register', 'wp_kama_user_register_action', 10, 2 );
function wp_kama_user_register_action( $user_id, $userdata ){
    global $__user_pass;
    $__user_pass = $userdata['user_pass'];
}

require_once "cli/service_function_cli.php";

add_action('cron_update_customer', 'update_customer');
//automatically add customer on user verified email
add_action( 'alg_wc_ev_user_account_activated', 'storeCustomerOnAccountActivated');

require_once "includes/notisend/notisendSender.php";
require_once "includes/notisend/NotisendOptionsPage.php";

if (!function_exists('storeCustomerOnAccountActivated')) {
	function storeCustomerOnAccountActivated( $user_id, $args=[] ) {

		$user = get_user_by('id', $user_id);
		if (!empty($user) && !empty($user->user_email)) {
			$data = [
				"email" => $user->user_email,
			];
			$notisendSettings = NotisendSettings::getSettings();
			createRecipients($data, "email/lists/$notisendSettings->group/recipients");
		}

		DataStore::update_registered_customer( $user_id );
	}
}

// #000018603
// валидация как и на фронте
add_filter('woocommerce_registration_errors', 'true_check_fields', 10, 3);
function true_check_fields($errors, $sanitized_user_login, $user_email)
{
    $email_validation_regex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";
    if (!preg_match($email_validation_regex, $user_email))
        $errors->add('user_email_error', __('Некорректный Email !', 'woocommerce'));
    if (isset($_POST['billing__inn']) && !INN::isValid($_POST['billing__inn']))
        $errors->add('billing__inn_error', __('Некорректный ИНН !', 'woocommerce'));

    return $errors;
}


/**
 * Проверяет ИНН (идентификационный номер налогоплательщика) на корректность
 *
 * @link     https://ru.wikipedia.org/wiki/ИНН
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   https://github.com/rin-nas
 * @charset  UTF-8
 * @version  1.0.4
 */
class INN
{
    /*
    ИНН    10 цифр для юр. лиц, 12 цифр для физ. лиц
    БИК    9 цифр
    Счёт   20 цифр
    КБК    20 цифр
    ОКАТО  от 4 до 11 цифр
    КПП    9 цифр
    */

    const IP = 1; // индивидуальный предприниматель
    const UL = 0; // юридическое лицо


    #запрещаем создание экземпляра класса, вызов методов этого класса только статически!
    private function __construct()
    {
    }

    /**
     *
     * @param scalar|null $n 10-ти или 12-ти значное целое число
     * @param int|null $type - тип плательщика ИП или ЮЛ. Если ЮЛ - то обязательно 10 знаков, если ИП то 12
     * @return  bool|null    TRUE, если ИНН корректен и FALSE в противном случае
     */
    public static function isValid($n, $type = null)
    {
        if ($n === null) return false;

        $n = strval($n);
        if (!ctype_digit($n)) {
            return false;
        }

        //все нули удовлетворяют формуле
        if ((int)$n === 0) {
            return false;
        }

        //не может быть региона 00
        if (substr($n, 0, 2) === '00') {
            return false;
        }

        $len = strlen($n);

        #10 знаков -- организации, для которых обязательно д.б. КПП
        if ($len === 10) {
            if ($type !== null && $type !== self::UL) {
                return false;
            }

            $sum = 0;
            foreach ([2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight) {
                $sum += $weight * $n[$i];
            }
            return $sum % 11 % 10 === (int)$n[9];
        }

        #12 знаков -- индивидуальные предприниматели, для которых КПП отсутствует
        if ($len === 12) {
            if ($type !== null && $type !== self::IP) {
                return false;
            }

            $sum1 = 0;
            foreach ([7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight) {
                $sum1 += $weight * $n[$i];
            }
            if ((($sum1 % 11) % 10) !== (int)$n[10]) {
                return false;
            }

            $sum2 = 0;
            foreach ([3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight) {
                $sum2 += $weight * $n[$i];
            }
            if ((($sum2 % 11) % 10) !== (int)$n[11]) {
                return false;
            }
            return true;
        }
        return false;
    }
}

add_action('admin_menu', function () {
    add_menu_page(
        'Капсулы',
        'Капсулы',
        'manage_options',
        'administrator',
        'kapsule_list',
        '',
        10);
});

require __DIR__ . '/includes/options.php';

require __DIR__ . '/includes/kapsule/xlsParser.php';
/**
 * @return void
 */
function sendKapsulsAjax(): void
{
    if(!empty($_FILES['loadXls']))  {
        $file_append = wp_handle_upload( $_FILES['loadXls'], [ 'test_form' => false ] );
        if ( ! empty( $file_append['file'] ) ) {

           parseWithAppendKapsuls($file_append['file']);
        }
    }
    wp_send_json( 'true', 200 );
}


add_action( 'wp_ajax_sendKapsulsAjax', 'sendKapsulsAjax' );
add_action( 'wp_ajax_nopriv_sendKapsulsAjax', 'sendKapsulsAjax' );