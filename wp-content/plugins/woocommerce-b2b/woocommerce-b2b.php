<?php

/*
    Plugin Name: WooCommerce B2B
    Plugin URI: https://code4life.it/shop/plugins/woocommerce-b2b/
    Description: Transform your WooCommerce into Business-to-Business shop for wholesale.
    Author: Code4Life
    Author URI: https://code4life.it/
    Version: 2.0.6
    Text Domain: woocommerce-b2b
    Domain Path: /i18n/
    License: GPLv3
    License URI: http://www.gnu.org/licenses/gpl-3.0.html

    WC requires at least: 3.0
    WC tested up to: 3.5.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/*******************
 * PLUGIN DEFAULTS *
 *******************/

// Add language support to internationalize plugin
add_action('plugins_loaded', function () {
    load_plugin_textdomain('woocommerce-b2b', false, dirname(plugin_basename(__FILE__)) . '/i18n/');
});

// Add link to configuration page into plugin
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    return array_merge(array(
        'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=wcb2b') . '">' . __('Settings', 'woocommerce-b2b') . '</a>'
    ), $links);
});

// Function to execute on plugin activation
register_activation_hook(__FILE__, function () {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : null;
    check_admin_referer('activate-plugin_' . $plugin);

    /* Code here */
    update_option('wcb2b_enable', 'yes');
    update_option('woocommerce_calc_taxes', 'yes');
    update_option('woocommerce_prices_include_tax', 'no');
    update_option('woocommerce_registration_generate_password', 'yes');
});

// Function to execute on plugin deactivation
register_deactivation_hook(__FILE__, function () {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : null;
    check_admin_referer('deactivate-plugin_' . $plugin);

    /* Code here */
    update_option('wcb2b_enable', 'no');
    update_option('wcb2b_notice_shown', 'no');
});

// Display recommended options notification on activation and dependencies messages
add_action('admin_notices', function () {
    // WooCommerce must be enabled
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        ?>
        <div class="notice notice-error">
        <p><?php _e('Warning! To use WooCommerce B2B it need WooCommerce is installed and active.', 'woocommerce-b2b'); ?></p>
        </div><?php
        return;
    }

    // Prevent updated settings notice is shown again
    if (get_option('wcb2b_notice_shown') !== 'yes') {
        ?>
        <div class="notice notice-info">
            <p>
                <?php printf(__('The following options are setted as recommended: %s', 'woocommerce-b2b'), '<p>' . implode('<br />', array(
                        __('WooCommerce settings > General > Enable taxes and tax calculations > Yes', 'woocommerce-b2b'),
                        __('WooCommerce settings > Tax > Prices entered with tax > No', 'woocommerce-b2b'),
                        __('WooCommerce settings > Accounts > Automatically generate customer password > Yes', 'woocommerce-b2b')
                    )) . '</p>'); ?>
            </p>
        </div>
        <?php
        // Updated settings notice is already shown
        update_option('wcb2b_notice_shown', 'yes');
    }
});

/******************
 * PLUGIN OPTIONS *
 ******************/

// Add new WooCommerce settings tab
add_filter('woocommerce_get_settings_pages', function ($settings) {
    $settings[] = include(plugin_dir_path(__FILE__) . 'includes/classes/class-wc-settings-wcb2b.php');
    return $settings;
});

// Register scripts & styles
add_action('admin_enqueue_scripts', function ($hook) {
    // Hook only on requested page
    if (strpos($hook, 'woocommerce_page_wc-settings') === false) {
        return;
    }

    // Support for minified versions
    $minified = SCRIPT_DEBUG === false ? '.min' : false;

    // Style
    wp_enqueue_style('wcb2b_settings', plugins_url('resources/css/wcb2b_settings' . $minified . '.css', __FILE__), array('woocommerce_admin_styles'), '1.0', 'all');
});

/****************************
 * PLUGIN FUNCTIONS & HOOKS *
 ****************************/

/*** IF PLUGIN IS ENABLED ***/
if (get_option('wcb2b_enable') === 'yes') {

    /* USERS FILTERS HOOK */

    // Filter users by custom settings
    add_action('manage_users_extra_tablenav', function ($which) {
        if ($which == 'bottom') {
            return;
        }

        if ($filters = apply_filters('wcb2b_users_extra_tablenav', false)) {
            // Add filter
            echo '<div class="alignleft actions">';
            echo $filters;
            submit_button(__('Filter', 'woocommerce-b2b'), '', 'filter', false);
            echo '</div>';
        }
    });

    // Add parameters to filter users
    add_filter('pre_get_users', function ($query) {
        global $pagenow;

        if ('users.php' !== $pagenow) {
            return;
        }

        // Initialize meta parameters
        $meta_query = array();

        // Add compare parameters for user group
        if (isset($_GET['wcb2b_group_filter']) && $_GET['wcb2b_group_filter'] !== '') {
            $meta_query[] = array(
                'key' => 'wcb2b_group',
                'value' => $_GET['wcb2b_group_filter']
            );
        }

        // Add compare parameters for user status
        if (isset($_GET['wcb2b_status_filter']) && $_GET['wcb2b_status_filter'] !== '') {
            // To retrieve inactive users, meta key couldn't exists!
            if ($_GET['wcb2b_status_filter'] == 0) {
                $meta_query[] = array(array(
                    'relation' => 'OR',
                    array(
                        'key' => 'wcb2b_status',
                        'value' => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'wcb2b_status',
                        'value' => $_GET['wcb2b_status_filter']
                    )));
            } else {
                $meta_query[] = array(
                    'key' => 'wcb2b_status',
                    'value' => $_GET['wcb2b_status_filter']
                );
            }
        }

        // Assign meta query
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    });

    /* PRODUCTS TAB */

    // Register scripts & styles for new product tab
    add_action('admin_enqueue_scripts', function ($hook) {
        // Hook only on requested page
        if (strpos($hook, 'post.php') === false && strpos($hook, 'post-new.php') === false) {
            return;
        }

        // Support for minified versions
        $minified = SCRIPT_DEBUG === false ? '.min' : false;

        // Style
        wp_enqueue_style('wcb2b_product', plugins_url('resources/css/wcb2b_product' . $minified . '.css', __FILE__), array('woocommerce_admin_styles'), '1.0', 'all');
    });

    // Add the new product tab (only on simple, variable, grouped)
    add_filter('woocommerce_product_data_tabs', function ($tabs) {
        $tabs['wcb2b_tab'] = array(
            'label' => __('B2B', 'woocommerce-b2b'),
            'target' => 'wcb2b_product_data',
            'class' => array('show_if_simple', 'show_if_variable', 'show_if_grouped', 'hide_if_external'),
            'priority' => 90
        );
        return $tabs;
    });

    // Add new field to manage packages and minimum quantity in WooCommerce products
    add_action('woocommerce_product_data_panels', function () {
        global $post_id;

        $wcb2b_step = get_post_meta($post_id, 'wcb2b_step', true);
        $wcb2b_min = get_post_meta($post_id, 'wcb2b_min', true);

        echo '<div id="wcb2b_product_data" class="panel woocommerce_options_panel">';
        woocommerce_wp_text_input(array(
            'id' => '_wcb2b_step',
            'type' => 'number',
            'name' => 'wcb2b_step',
            'label' => __('Package', 'woocommerce-b2b'),
            'description' => __('Force customers to purchase product by pack increment. Insert how much products are in every pack', 'woocommerce-b2b'),
            'desc_tip' => true,
            'value' => !empty($wcb2b_step) ? $wcb2b_step : 1,
            'required' => true,
            'custom_attributes' => array(
                'min' => 1
            )
        ));
        woocommerce_wp_text_input(array(
            'id' => '_wcb2b_min',
            'type' => 'number',
            'name' => 'wcb2b_min',
            'label' => __('Minimum quantity', 'woocommerce-b2b'),
            'description' => __('Force customers to purchase minimum quantity of this product', 'woocommerce-b2b'),
            'desc_tip' => true,
            'value' => !empty($wcb2b_min) ? $wcb2b_min : 1,
            'required' => true,
            'custom_attributes' => array(
                'min' => 1
            )
        ));
        echo '</div>';
    });

    // Save value into database
    add_action('woocommerce_process_product_meta', function ($product_id) {
        if (!empty($_POST['wcb2b_step'])) {
            // Validate field
            $wcb2b_step = intval($_POST['wcb2b_step']);
            update_post_meta($product_id, 'wcb2b_step', $wcb2b_step);
        }
        if (!empty($_POST['wcb2b_min'])) {
            // Validate field
            $wcb2b_min = intval($_POST['wcb2b_min']);
            update_post_meta($product_id, 'wcb2b_min', $wcb2b_min);
        }
    });

    // Add min and pack amounts to WooCommerce frontend
    add_filter('woocommerce_quantity_input_args', function ($args, $product) {
        if ($wcb2b_step = get_post_meta($product->get_id(), 'wcb2b_step', true)) {
            $args['step'] = intval($wcb2b_step);
        }
        if ($wcb2b_min = get_post_meta($product->get_id(), 'wcb2b_min', true)) {
            $args['min_value'] = intval($wcb2b_min);
            $args['input_value'] = intval($wcb2b_min);
        }

        return $args;
    }, 10, 2);

    // Add min and pack amount message to product
    add_filter('woocommerce_after_add_to_cart_form', function () {
        // If messages can be displayed
        if (apply_filters('wcb2b_display_quantity_message', true)) {
            global $product;

            // If is set min purchase value, display message
            $wcb2b_min = get_post_meta($product->get_id(), 'wcb2b_min', true);
            if ($wcb2b_min && $wcb2b_min > 1) {
                echo '<p class="wcb2b_minimum_message">' . apply_filters('wcb2b_minimum_message', sprintf(__('You must purchase at least %s of this product', 'woocommerce-b2b'), $wcb2b_min), $wcb2b_min) . '</p>';
            }

            // If is set increment purchase value, display message
            $wcb2b_step = get_post_meta($product->get_id(), 'wcb2b_step', true);
            if ($wcb2b_step && $wcb2b_step > 1) {
                echo '<p class="wcb2b_increment_message">' . apply_filters('wcb2b_increment_message', sprintf(__('This product can be purchased by increments of %s', 'woocommerce-b2b'), $wcb2b_step), $wcb2b_step) . '</p>';
            }
        }
    });

    /* HIDE PRICES TO NOT LOGGED IN CUSTOMERS */

    if (get_option('wcb2b_hide_prices') === 'yes') {

        // Display login message to guest users
        function wcb2b_login_message()
        {
            // If messages can be displayed
            if (apply_filters('wcb2b_display_login_message', true)) {
                echo '<p class="wcb2b_login_message"><a href="' . apply_filters('wcb2b_login_message_url', get_permalink(get_option('woocommerce_myaccount_page_id'))) . '">' . apply_filters('wcb2b_login_message', __('Please, login to see prices and buy', 'woocommerce-b2b')) . '</a><p>';
            }
        }

        // Remove all possibilities to buy if user is not logged in
        add_action('woocommerce_init', function () {
            // Skip if user is logged in
            if (is_user_logged_in()) {
                return;
            }

            // Remove sale flash and price from loop
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

            // Remove sale flash and price from single product
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

            // Remove prices also for grouped and variable products
            add_filter('woocommerce_get_price_html', '__return_false');

            // Remove add to cart button to variable products
            remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);

            // Add a link to login page with message
            add_action('woocommerce_after_shop_loop_item', 'wcb2b_login_message');
            add_action('woocommerce_single_product_summary', 'wcb2b_login_message');

            // Any product can be purchased
            add_filter('woocommerce_is_purchasable', '__return_false');

            // Fix to remove sale flash message from loop
            add_filter('woocommerce_sale_flash', '__return_false');

            // Remove all product from cart
            add_action('woocommerce_loaded', function () {
                wc_empty_cart();
            });

            // Remove offers object if prices are hidden
            add_filter('woocommerce_structured_data_product', function ($markup) {
                unset($markup['offers']);
                return $markup;
            });

            // Remove price filter for guests
            add_action('widgets_init', function () {
                unregister_widget('WC_Widget_Price_Filter');
            }, 99);
        });
    }

    /* VAT NUMBER FIELD */

    if (get_option('wcb2b_add_vatnumber') === 'yes') {

        // Add VAT number field to billing address (in checkout)
        add_filter('woocommerce_billing_fields', function ($fields) {
            // Company is mandatory (only for WC < 3.4.0)
            if (version_compare('3.4.0', WC_VERSION)) {
                $fields['billing_company']['required'] = apply_filters('wcb2b_billing_company_required', true);
            }

            // Add field exactly after company field
            $fields += array('billing_vat' => array(
                'type' => 'text',
                'label' => __('Vat number', 'woocommerce-b2b'),
                'placeholder' => _x('Vat number', 'placeholder', 'woocommerce-b2b'),
                'required' => true,
                'class' => array('form-row-wide'),
                'clear' => true,
                'priority' => 35
            ));
            return $fields;
        });

        // Add VAT number field to billing address (in My account)
        add_filter('woocommerce_my_account_my_address_formatted_address', function ($fields, $customer_id, $type) {
            if ($type == 'billing') {
                $fields['vat'] = get_user_meta($customer_id, 'billing_vat', true);
            }
            return $fields;
        }, 10, 3);

        // Add VAT number field to billing address (in Order received)
        add_filter('woocommerce_order_formatted_billing_address', function ($fields, $order) {
            $fields['vat'] = get_post_meta($order->get_id(), '_billing_vat', true);
            return $fields;
        }, 10, 2);

        // Creating merger VAT number variables for printing formatting
        add_filter('woocommerce_formatted_address_replacements', function ($address, $args) {
            $address['{vat}'] = '';
            $address['{vat_upper}'] = '';

            if (!empty($args['vat'])) {
                $address['{vat}'] = $args['vat'];
                $address['{vat_upper}'] = strtoupper($args['vat']);
            }
            return $address;
        }, 10, 2);

        // Redefine the formatting to print the address, including VAT number.
        add_filter('woocommerce_localisation_address_formats', function ($formats) {
            return str_replace("{company}", "{company}\n{vat_upper}", $formats);
        });

        // Add VAT number field to billing address (in admin: Customer profile)
        add_filter('woocommerce_customer_meta_fields', function ($fields) {
            // Add field exactly after company field
            $fields['billing']['fields'] = array_slice($fields['billing']['fields'], 0, 3, true)
                + array('billing_vat' => array(
                    'label' => __('VAT number', 'woocommerce-b2b'),
                    'description' => '',
                ))
                + array_slice($fields['billing']['fields'], 3, count($fields['billing']['fields']) - 1, true);
            return $fields;
        });

        // Add VAT number field to billing address (in admin: Order page)
        add_filter('woocommerce_admin_billing_fields', function ($fields) {
            // Add field exactly after company field
            $fields = array_slice($fields, 0, 3, true)
                + array('vat' => array(
                    'label' => __('VAT number', 'woocommerce-b2b'),
                    'show' => true,
                ))
                + array_slice($fields, 3, count($fields) - 1, true);
            return $fields;
        });

        // Filter to copy the VAT number field from user meta fields to the order admin form (after clicking dedicated button on admin page)
        add_filter('woocommerce_found_customer_details', function ($customer_data) {
            $customer_data['billing_vat'] = get_user_meta($_POST['user_id'], 'billing_vat', true);
            return $customer_data;
        });
    }


    /* MIN PURCHASE AMOUNT */
        // Enterego (V.Mikheev) hide check count sum in cart if order have preodred porduct
    if (get_option('wcb2b_min_purchase_amount') > 0) {


        // If min amount is not reached, disable go to checkout button (cart)
        add_action('woocommerce_before_cart', function () {

            // Check if option is active and minimum amount is reached or not
            if (floatval(get_option('wcb2b_min_purchase_amount')) > floatval(WC()->cart->get_cart_contents_total())) {
                $items = WC()->cart->get_cart();
                foreach ($items as $item => $values) {
                    $itPreorder = 0;
                    $price = get_post_meta($values['product_id'], '_price', true);
                    if ($price == 0) {
                        $itPreorder = 1;
                    }
                }
                if (!$itPreorder) {
                    // Add a message to inform that minimum amount is not reached yet (in cart)
                    if (get_option('wcb2b_display_min_purchase_cart_message') === 'yes') {
                        add_action('woocommerce_proceed_to_checkout', function () {
                            $min_price_raw = get_option('wcb2b_min_purchase_amount');
                            $min_price = wc_price($min_price_raw);
                            echo '<p class="wcb2b_display_min_purchase_cart_message">' . apply_filters('wcb2b_display_min_purchase_cart_message', sprintf(__('To proceed to checkout and complete your purchase, make sure you have reached the minimum amount of %s.', 'woocommerce-b2b'), $min_price), $min_price_raw, $min_price) . '<p>';
                        }, 20);

                        // Remove "Proceed to checkout" button (also in mini-cart)
                        if (get_option('wcb2b_prevent_checkout_button') === 'yes') {
                            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
                            remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20);
                        }
                    }
                }

            }
        });

        // If min amount is not reached, place order button (checkout)
        add_action('woocommerce_before_checkout_form', function () {
            $items = WC()->cart->get_cart();
            foreach ($items as $item => $values) {
                $itPreorder = 0;
                $price = get_post_meta($values['product_id'], '_price', true);
                if ($price == 0) {
                    $itPreorder = 1;
                }
            }
            if (!$itPreorder) {
                if (floatval(get_option('wcb2b_min_purchase_amount')) > floatval(WC()->cart->get_cart_contents_total())) {
                    // Add a message to inform that minimum amount is not reached yet (in checkout). It replace the "Place order" button
                    remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
                    add_action('woocommerce_checkout_order_review', function () {
                        $min_price_raw = get_option('wcb2b_min_purchase_amount');
                        $min_price = wc_price($min_price_raw);
                        echo '<p class="wcb2b_display_min_purchase_checkout_message">' . apply_filters('wcb2b_display_min_purchase_checkout_message', sprintf(__('To proceed to checkout and complete your purchase, you must reach the minimum amount of %s, but your total cart amount is currently %s. Return to %sshop%s', 'woocommerce-b2b'), $min_price, wc_price(WC()->cart->cart_contents_total), '<a href="' . get_permalink(wc_get_page_id('shop')) . '">', '</a>'), $min_price_raw, $min_price) . '<p>';
                    });
                }
            }
        });
    }


    /* EXTENDED REGISTRATION FORM */

    if (get_option('wcb2b_extend_registration_form') === 'yes') {

        // Add extended fields to customer registration
        add_action('woocommerce_register_form_start', function () {
            wp_enqueue_script('wc-country-select');
            wp_enqueue_script('wc-address-i18n');
            wp_enqueue_script('wc-checkout');

            $checkout = WC_Checkout::instance();
            $fields = apply_filters('wcb2b_register_form_fields', $checkout->get_checkout_fields('billing'));

            foreach ($fields as $key => $field) {
                // Prevent email field replication
                if ($key == 'billing_email') {
                    continue;
                }

                if (isset($field['country_field'], $fields[$field['country_field']])) {
                    $field['country'] = $checkout->get_value($field['country_field']);
                }
                woocommerce_form_field($key, $field, $checkout->get_value($key));
            }
        });

        // Validate extended fields
        add_action('woocommerce_register_post', function ($username, $email, $errors) {
            $checkout = WC_Checkout::instance();
            $fields = $checkout->get_checkout_fields('billing');
            $data = $_POST;

            foreach ($fields as $key => $field) {
                if ($key == 'billing_email') {
                    continue;
                }

                $required = !empty($field['required']);
                $format = array_filter(isset($field['validate']) ? (array)$field['validate'] : array());
                $field_label = isset($field['label']) ? $field['label'] : '';

                if (in_array('postcode', $format)) {
                    $country = isset($data['billing_country']) ? $data['billing_country'] : WC()->customer->{"get_billing_country"}();
                    $data[$key] = wc_format_postcode($data[$key], $country);

                    if ('' !== $data[$key] && !WC_Validation::is_postcode($data[$key], $country)) {
                        $errors->add('validation', sprintf(__('%s is not a valid postcode / ZIP.', 'woocommerce-b2b'), '<strong>' . esc_html($field_label) . '</strong>'));
                    }
                }

                if (in_array('phone', $format)) {
                    $data[$key] = wc_format_phone_number($data[$key]);

                    if ('' !== $data[$key] && !WC_Validation::is_phone($data[$key])) {
                        /* translators: %s: phone number */
                        $errors->add('validation', sprintf(__('%s is not a valid phone number.', 'woocommerce-b2b'), '<strong>' . esc_html($field_label) . '</strong>'));
                    }
                }

                if (in_array('email', $format) && '' !== $data[$key]) {
                    $data[$key] = sanitize_email($data[$key]);
                    if (!is_email($data[$key])) {
                        /* translators: %s: email address */
                        $errors->add('validation', sprintf(__('%s is not a valid email address.', 'woocommerce-b2b'), '<strong>' . esc_html($field_label) . '</strong>'));
                        continue;
                    }
                }

                if ('' !== $data[$key] && in_array('state', $format)) {
                    $country = isset($data['billing_country']) ? $data['billing_country'] : WC()->customer->{"get_billing_country"}();
                    $valid_states = WC()->countries->get_states($country);

                    if (!empty($valid_states) && is_array($valid_states) && sizeof($valid_states) > 0) {
                        $valid_state_values = array_map('wc_strtoupper', array_flip(array_map('wc_strtoupper', $valid_states)));
                        $data[$key] = wc_strtoupper($data[$key]);

                        if (isset($valid_state_values[$data[$key]])) {
                            // With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
                            $data[$key] = $valid_state_values[$data[$key]];
                        }

                        if (!in_array($data[$key], $valid_state_values)) {
                            /* translators: 1: state field 2: valid states */
                            $errors->add('validation', sprintf(__('%1$s is not valid. Please enter one of the following: %2$s', 'woocommerce-b2b'), '<strong>' . esc_html($field_label) . '</strong>', implode(', ', $valid_states)));
                        }
                    }
                }

                if ($required && '' === $data[$key]) {
                    /* translators: %s: field name */
                    $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce-b2b'), '<strong>' . esc_html($field_label) . '</strong>'), $field_label));
                }
            }
            return $errors;
        }, 10, 3);

        // Save extended fields to customer registration
        add_action('woocommerce_created_customer', function ($customer_id) {
            $checkout = WC_Checkout::instance();
            $fields = $checkout->get_checkout_fields('billing');

            foreach ($fields as $key => $field) {
                // Prevent email field replication
                if ($key == 'billing_email') {
                    continue;
                }

                update_user_meta($customer_id, $key, sanitize_text_field($_POST[$key]));
            }
        });
    }

    if (get_option('wcb2b_registration_notice') === 'yes') {

        // Send new registration notice to admin by email
        add_action('woocommerce_created_customer', function ($customer_id) {
            wp_new_user_notification($customer_id, null, 'admin');
        });
    }

    /* GROUPS AND DISCOUNTS */

    // Retrieve all groups
    function wcb2b_get_groups()
    {
        return new WP_Query(array(
            'post_type' => array('wcb2b_group'),
            'post_status' => array('publish'),
            'posts_per_page' => -1
        ));
    }

    // Register scripts & styles
    add_action('admin_enqueue_scripts', function ($hook) {
        // Only ShopManager can manage groups
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        // Hook only on requested page
        if (strpos($hook, 'users.php') === false) {
            return;
        }

        // Support for minified versions
        $minified = SCRIPT_DEBUG === false ? '.min' : false;

        $wcb2b_groups = wcb2b_get_groups();
        wp_enqueue_script('wcb2b_user_groups', plugins_url('resources/js/wcb2b_user_groups' . $minified . '.js', __FILE__), false, '1.0', true);
        wp_localize_script('wcb2b_user_groups', 'wcb2b_groups_parameters', array(
            'wcb2b_groups' => json_encode(wp_list_pluck($wcb2b_groups->posts, 'post_title', 'ID'))
        ));
    });

    // Register scripts & styles
    add_action('admin_enqueue_scripts', function ($hook) {
        // Only ShopManager can manage groups
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        // Hook only on requested page
        if (strpos($hook, 'post.php') === false) {
            return;
        }

        // Support for minified versions
        $minified = SCRIPT_DEBUG === false ? '.min' : false;

        $wcb2b_groups = wcb2b_get_groups();
        wp_enqueue_style('wcb2b_groups', plugins_url('resources/css/wcb2b_groups' . $minified . '.css', __FILE__), array('woocommerce_admin_styles'), '1.0', 'all');
    });

    // Register groups post type
    add_action('init', function () {
        $labels = array(
            'name' => __('Groups', 'woocommerce-b2b'),
            'singular_name' => __('Group', 'woocommerce-b2b'),
            'all_items' => __('All Groups', 'woocommerce-b2b'),
            'menu_name' => _x('Groups', 'Admin menu name', 'woocommerce-b2b'),
            'add_new' => __('Add New', 'woocommerce-b2b'),
            'add_new_item' => __('Add new group', 'woocommerce-b2b'),
            'edit' => __('Edit', 'woocommerce-b2b'),
            'edit_item' => __('Edit group', 'woocommerce-b2b'),
            'new_item' => __('New group', 'woocommerce-b2b'),
            'view_item' => __('View group', 'woocommerce-b2b'),
            'view_items' => __('View groups', 'woocommerce-b2b'),
            'search_items' => __('Search groups', 'woocommerce-b2b'),
            'not_found' => __('No groups found', 'woocommerce-b2b'),
            'not_found_in_trash' => __('No groups found in trash', 'woocommerce-b2b'),
            'parent' => __('Parent group', 'woocommerce-b2b'),
            'featured_image' => __('Group image', 'woocommerce-b2b'),
            'set_featured_image' => __('Set group image', 'woocommerce-b2b'),
            'remove_featured_image' => __('Remove group image', 'woocommerce-b2b'),
            'use_featured_image' => __('Use as group image', 'woocommerce-b2b'),
            'insert_into_item' => __('Insert into group', 'woocommerce-b2b'),
            'uploaded_to_this_item' => __('Uploaded to this group', 'woocommerce-b2b'),
            'filter_items_list' => __('Filter groups', 'woocommerce-b2b'),
            'items_list_navigation' => __('Groups navigation', 'woocommerce-b2b'),
            'items_list' => __('Groups list', 'woocommerce-b2b')
        );
        $args = array(
            'label' => __('Group', 'woocommerce-b2b'),
            'description' => __('This is where you can add new groups to your customers.', 'woocommerce-b2b'),
            'labels' => $labels,
            'supports' => array('title'),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'users.php',
            'menu_position' => 99,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'product',
            'map_meta_cap' => true
        );
        register_post_type('wcb2b_group', $args);
    }, 0);

    // Add custom messages for group post type
    add_filter('post_updated_messages', function ($messages) {
        $post = get_post();
        $post_type = get_post_type($post);
        $post_type_object = get_post_type_object($post_type);

        $messages['wcb2b_group'] = array(
            0 => '', // Unused
            1 => __('Group updated.', 'woocommerce-b2b'),
            2 => __('Group updated.', 'woocommerce-b2b'),
            3 => __('Group deleted.', 'woocommerce-b2b'),
            4 => __('Group updated.', 'woocommerce-b2b'),
            5 => isset($_GET['revision']) ? sprintf(
                __('Group restored to revision from %s', 'woocommerce-b2b'),
                wp_post_revision_title((int)$_GET['revision'], false)
            ) : false,
            6 => __('Group published.', 'woocommerce-b2b'),
            7 => __('Group saved.', 'woocommerce-b2b'),
            8 => __('Group submitted.', 'woocommerce-b2b'),
            9 => sprintf(
                __('Group scheduled for: <strong>%1$s</strong>.', 'woocommerce-b2b'),
                date_i18n(get_option('date_format'), strtotime($post->post_date))
            ),
            10 => __('Group draft updated.', 'woocommerce-b2b')
        );

        if ($post_type_object->publicly_queryable && 'wcb2b_group' === $post_type) {
            $permalink = get_permalink($post->ID);

            $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View group', 'woocommerce-b2b'));
            $messages[$post_type][1] .= $view_link;
            $messages[$post_type][6] .= $view_link;
            $messages[$post_type][9] .= $view_link;

            $preview_permalink = add_query_arg('preview', 'true', $permalink);
            $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview group', 'woocommerce-b2b'));
            $messages[$post_type][8] .= $preview_link;
            $messages[$post_type][10] .= $preview_link;
        }
        return $messages;
    });

    // Add new group column
    add_filter('manage_wcb2b_group_posts_columns', function ($columns) {
        return array_slice($columns, 0, -1, true) +
            array('wcb2b_discount' => __('Discount', 'woocommerce-b2b')) +
            array_slice($columns, -1, null, true);
    });

    // Retrieve group discount column value
    add_filter('manage_wcb2b_group_posts_custom_column', function ($column, $post_id) {
        if ($column === 'wcb2b_discount') {
            echo get_post_meta($post_id, 'wcb2b_group_discount', true);
        }
    }, 10, 2);

    // Make discount group column sortable
    add_filter('manage_edit-wcb2b_group_sortable_columns', function ($columns) {
        $columns['wcb2b_discount'] = 'wcb2b_discount';
        return $columns;
    });

    // Display discount percentage in user area
    add_action('woocommerce_account_navigation', function () {
        // If logged user isn't customer, skip
        if (!in_array('customer', get_userdata(get_current_user_id())->roles)) {
            return;
        }

        // If option is active
        if (get_option('wcb2b_show_customer_discount') === 'yes') {
            // Retrieve group data
            $wcb2b_user_group = get_the_author_meta('wcb2b_group', get_current_user_id(), true);
            $wcb2b_group = get_post($wcb2b_user_group);

            // Check if group exists or is deleted precedently
            if (false === get_post_status($wcb2b_user_group)) {
                return;
            }

            // If has discount assigned, display
            if ($wcb2b_discount = get_post_meta($wcb2b_group->ID, 'wcb2b_group_discount', true)) {
                $wcb2b_discount = number_format($wcb2b_discount,
                    wc_get_price_decimals(),
                    wc_get_price_decimal_separator(),
                    wc_get_price_thousand_separator()
                );
                echo '<div class="wcb2b-discount-amount">' . apply_filters('wcb2b_discount_message', sprintf(__('Discount amount assigned to you: %s%%', 'woocommerce-b2b'), $wcb2b_discount), $wcb2b_discount) . '</div><br />';
            }
        }
    });

    // Display discount percentage in product page
    add_action('woocommerce_single_product_summary', function () {
        // If logged user isn't customer, skip
        if (!is_user_logged_in()) {
            return;
        }
        if (!in_array('customer', get_userdata(get_current_user_id())->roles)) {
            return;
        }

        // If option is active
        if (get_option('wcb2b_show_customer_discount_product') === 'yes') {
            // Retrieve group data
            $wcb2b_user_group = get_the_author_meta('wcb2b_group', get_current_user_id(), true);
            $wcb2b_group = get_post($wcb2b_user_group);

            // Check if group exists or is deleted precedently
            if (false === get_post_status($wcb2b_user_group)) {
                return;
            }

            // If has discount assigned, display
            if ($wcb2b_discount = get_post_meta($wcb2b_group->ID, 'wcb2b_group_discount', true)) {
                $wcb2b_discount = number_format($wcb2b_discount,
                    wc_get_price_decimals(),
                    wc_get_price_decimal_separator(),
                    wc_get_price_thousand_separator()
                );
                echo '<div class="wcb2b-discount-amount">' . apply_filters('wcb2b_discount_message', sprintf(__('Discount amount assigned to you: %s%%', 'woocommerce-b2b'), $wcb2b_discount), $wcb2b_discount) . '</div><br />';
            }
        }
    }, 15);

    // Apply group discount to every product final price (Simple)
    add_filter('woocommerce_product_get_price', 'wcb2b_get_discounted_price', 99, 2);
    // Apply group discount to every product final price (Variable)
    add_filter('woocommerce_product_variation_get_price', 'wcb2b_get_discounted_price', 99, 2);
    // Apply group discount to every product final price (Variation)
    add_filter('woocommerce_variation_prices_price', 'wcb2b_get_discounted_price', 99, 2);
    function wcb2b_get_discounted_price($price, $object)
    {
        // Check if product price are already discounted
        if (!property_exists($object, 'wcb2b_is_discounted_price')) {
            // Only ShopManager can view full prices
            if (!current_user_can('manage_woocommerce')) {
                // Apply discount to customers
                if (is_user_logged_in()) {
                    // Retrieve group data
                    $wcb2b_user_group = get_the_author_meta('wcb2b_group', get_current_user_id(), true);
                    $wcb2b_group = get_post($wcb2b_user_group);

                    // Check if group exists or is deleted precedently
                    if (false !== get_post_status($wcb2b_user_group)) {
                        // Apply percentage discount
                        if (!property_exists($object, 'wcb2b_is_discounted_price')) {
                            // If has discount assigned, calculate new price
                            if ($wcb2b_discount = get_post_meta($wcb2b_group->ID, 'wcb2b_group_discount', true)) {
                                // Calculate discounted product final price and update product property
                                $wcb2b_discounted_price = $wcb2b_discount ? $price - ($price * $wcb2b_discount / 100) : $price;
                                $object->set_price($wcb2b_discounted_price);

                                // Retrieve other prices
                                $regular_price = $object->get_regular_price();
                                $sale_price = $object->get_sale_price();

                                // Calculate discounted product other prices and update product properties
                                if (!empty($regular_price)) {
                                    $wcb2b_discounted_regular_price = $wcb2b_discount ? $regular_price - ($regular_price * $wcb2b_discount / 100) : $regular_price;
                                    $object->set_regular_price($wcb2b_discounted_regular_price);
                                }
                                if (!empty($sale_price)) {
                                    $wcb2b_discounted_sale_price = $wcb2b_discount ? $sale_price - ($sale_price * $wcb2b_discount / 100) : $sale_price;
                                    $object->set_sale_price($wcb2b_discounted_sale_price);
                                }

                                // Trick: set property to prevent multiple discount application!
                                $object->wcb2b_is_discounted_price = 1;

                                return $wcb2b_discounted_price;
                            }
                        }
                    }
                }
            }
        }
        return $price;
    }

    // Create hash for prices cache
    add_filter('woocommerce_get_variation_prices_hash', function ($hash) {
        if (is_user_logged_in()) {
            $hash[] = get_current_user_id();
        }
        return $hash;
    });

    // Add group field to user edit page
    add_action('show_user_profile', 'wcb2b_user_profile_group');
    add_action('edit_user_profile', 'wcb2b_user_profile_group');
    function wcb2b_user_profile_group($user)
    {
        // Only ShopManager can edit customer group
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        // If user isn't a customer, skip. Only customers have group field
        if (!in_array('customer', get_userdata($user->ID)->roles)) {
            return;
        }

        $wcb2b_groups = wcb2b_get_groups();
        ?>
        <h3><?php _e('User group', 'woocommerce-b2b'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="wcb2b_group"><?php _e('Group', 'woocommerce-b2b'); ?></label></th>
                <td>
                    <select name="wcb2b_group" id="wcb2b_group" class="regular-text">
                        <option value="">---</option>
                        <?php if ($wcb2b_groups->have_posts()) : ?>
                            <?php while ($wcb2b_groups->have_posts()) : $wcb2b_groups->the_post(); ?>
                                <option value="<?php the_ID(); ?>" <?php selected(get_the_author_meta('wcb2b_group', $user->ID, true), get_the_ID()); ?> ><?php the_title(); ?></option>
                            <?php endwhile; ?>
                        <?php endif;
                        wp_reset_postdata(); ?>
                    </select>
                    <br/>
                    <span class="description"><?php _e('Please select user group to apply discounts.', 'woocommerce-b2b'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    // Update user group
    add_action('personal_options_update', 'wcb2b_user_update_group');
    add_action('edit_user_profile_update', 'wcb2b_user_update_group');
    function wcb2b_user_update_group($user_id)
    {
        // Only ShopManager can edit customer group
        if (!current_user_can('manage_woocommerce')) {
            return false;
        }

        // If user isn't a customer, skip
        if (!in_array('customer', get_userdata($user_id)->roles)) {
            return false;
        }

        // Assign customer to group
        update_user_meta($user_id, 'wcb2b_group', intval($_POST['wcb2b_group']));
    }

    // Add new group column
    add_filter('manage_users_columns', function ($columns) {
        $columns['wcb2b_group'] = __('Group', 'woocommerce-b2b');
        return $columns;
    });

    // Retrieve user group column value
    add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {
        // If user isn't a customer, skip. Only customers have group field
        if (!in_array('customer', get_userdata($user_id)->roles)) {
            return $value;
        }

        if ($column_name === 'wcb2b_group') {
            // Retrieve group data
            if (!$wcb2b_user_group = get_the_author_meta('wcb2b_group', $user_id)) {
                return;
            }
            $wcb2b_group = get_post($wcb2b_user_group);

            // Check if group exists or is deleted precedently
            if (false === get_post_status($wcb2b_user_group)) {
                return;
            }

            if ($wcb2b_group->post_type == 'wcb2b_group') {
                return get_the_title($wcb2b_group->ID);
            }
        }
        return $value;
    }, 10, 3);

    // Make group column sortable
    add_filter('manage_users_sortable_columns', function ($columns) {
        $columns['wcb2b_group'] = 'wcb2b_group';
        return $columns;
    });

    // Bulk assignment to group
    add_filter('bulk_actions-users', function ($actions) {
        // Only ShopManager can edit customer group
        if (!current_user_can('manage_woocommerce')) {
            return $actions;
        }

        $actions['wcb2b-assign_group-action'] = __('Assign group', 'woocommerce-b2b');
        return $actions;
    });

    // Process new group assignment and status change and return updated users
    add_filter('handle_bulk_actions-users', function ($redirect_url, $action, $user_ids) {
        // Only ShopManager can edit customer group
        if (!current_user_can('manage_woocommerce')) {
            return $redirect_url;
        }

        // Bulk change group handle
        if ($action == 'wcb2b-assign_group-action' && isset($_REQUEST['wcb2b_group'])) {
            $wcb2b_group = $_REQUEST['wcb2b_group'];

            // Validate request
            if (!is_numeric($wcb2b_group)) {
                return $redirect_url;
            }

            // For each user selected, update group
            foreach ($user_ids as $user_id) {
                // Skip if user hasn't a customer role
                if (!in_array('customer', get_userdata($user_id)->roles)) {
                    continue;
                }

                // Update group
                update_user_meta($user_id, 'wcb2b_group', intval($wcb2b_group));
            }
        }
        return $redirect_url;
    }, 10, 3);

    // Add filter by group
    add_filter('wcb2b_users_extra_tablenav', function ($filters) {
        $filters .= '<select name="wcb2b_group_filter" id="wcb2b_group_filter">';
        $filters .= '<option value="">' . esc_html__('Filter by group', 'woocommerce-b2b') . '</option>';

        // Retrieve groups
        $wcb2b_groups = wcb2b_get_groups();
        if ($wcb2b_groups->have_posts()) {
            while ($wcb2b_groups->have_posts()) {
                $wcb2b_groups->the_post();

                $filters .= sprintf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    get_the_ID(),
                    (isset($_GET['wcb2b_group_filter']) && $_GET['wcb2b_group_filter'] != '' && intval($_GET['wcb2b_group_filter']) === get_the_ID() ? ' selected="selected"' : ''),
                    get_the_title()
                );
            }
        }
        $filters .= '</select>';

        // Restore original Post Data
        wp_reset_postdata();

        return $filters;
    });

    // Add metaboxes for groups
    add_action('add_meta_boxes_wcb2b_group', function ($post) {
        // Discount meta box
        add_meta_box('wcb2b_group-discount-meta_box', __('Discount (%)', 'woocommerce-b2b'), function ($post) {
            // Make sure the form request comes from WordPress
            wp_nonce_field(basename(__FILE__), 'wcb2b_group-discount-nonce');

            // Retrieve group discount current value
            $wcb2b_group_discount = get_post_meta($post->ID, 'wcb2b_group_discount', true);

            // Display fields
            printf('<p><input type="text" name="wcb2b_group_discount" value="%s" pattern="[0-9]+([\.][0-9]+)?" title="' . __('This should be a number with up to 2 decimal places, with (.) as decimal separator', 'woocommerce-b2b') . '" /></p>',
                $wcb2b_group_discount
            );
        }, 'wcb2b_group', 'side', 'low');

        // Payment methods meta box
        add_meta_box('wcb2b_group-gateways-meta_box', __('Disable payment methods for this group', 'woocommerce-b2b'), function ($post) {
            // Make sure the form request comes from WordPress
            wp_nonce_field(basename(__FILE__), 'wcb2b_group-gateways-nonce');

            if ($gateways = WC()->payment_gateways->get_available_payment_gateways()) {
                // Retrieve group allowed gateways value
                $wcb2b_group_gateways = get_post_meta($post->ID, 'wcb2b_group_gateways', true);

                foreach ($gateways as $gateway) {
                    // Display fields
                    printf('<p><input type="checkbox" name="wcb2b_group_gateways[]" value="%s" %s /> %s</p>',
                        $gateway->id,
                        is_array($wcb2b_group_gateways) ? checked(in_array($gateway->id, $wcb2b_group_gateways), true, false) : false,
                        $gateway->title
                    );
                }
            }
        }, 'wcb2b_group', 'normal', 'low');

        if (get_option('wcb2b_product_cat_visibility') === 'yes') {

            // Product category meta box
            add_meta_box('wcb2b_group-taxonomy-meta_box', __('Product categories visibility for this group', 'woocommerce-b2b'), function ($post) {
                if ($terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'bypass' => 1))) {
                    foreach ($terms as $term) {
                        $wcb2b_group_visibility = get_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', true);
                        $is_visibile = is_array($wcb2b_group_visibility) ? in_array($post->ID, $wcb2b_group_visibility) : false;

                        // Display fields
                        printf('<p><a href="%s"><span class="dashicons dashicons-external"></span></a> %s</p>',
                            get_edit_term_link($term->term_id, 'product_cat'),
                            (!$is_visibile ? '<s>' : false) . $term->name . (!$is_visibile ? '</s>' : false)
                        );
                    }
                }
            }, 'wcb2b_group', 'normal', 'low');

        }

    });

    // Remove not allowed payment gateways
    add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
        if (!is_user_logged_in()) {
            return $available_gateways;
        }

        // ShopManager can view all
        if (current_user_can('manage_woocommerce')) {
            return $available_gateways;
        }

        // Retrieve customer group
        $wcb2b_user_group = get_the_author_meta('wcb2b_group', get_current_user_id(), true);

        // Retrieve group allowed gateways value
        $wcb2b_group_gateways = get_post_meta($wcb2b_user_group, 'wcb2b_group_gateways', true);
        if (is_array($wcb2b_group_gateways)) {
            foreach ($available_gateways as $available_gateway => $data) {
                // Disable payment method
                if (in_array($available_gateway, $wcb2b_group_gateways)) {
                    unset($available_gateways[$available_gateway]);
                }
            }
        }
        return $available_gateways;
    });

    // Store custom field meta box data
    add_action('save_post_wcb2b_group', function ($post_id) {
        // Only ShopManager can edit customer group
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        // Verify meta box nonce
        if (!isset($_POST['wcb2b_group-discount-nonce']) || !wp_verify_nonce($_POST['wcb2b_group-discount-nonce'], basename(__FILE__))) {
            return;
        }
        if (!isset($_POST['wcb2b_group-gateways-nonce']) || !wp_verify_nonce($_POST['wcb2b_group-gateways-nonce'], basename(__FILE__))) {
            return;
        }

        // Return if autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Store group discount
        if (isset($_POST['wcb2b_group_discount'])) {
            update_post_meta($post_id, 'wcb2b_group_discount', wc_format_decimal($_POST['wcb2b_group_discount']));
        }
        // Store group gateways
        if (isset($_POST['wcb2b_group_gateways'])) {
            update_post_meta($post_id, 'wcb2b_group_gateways', $_POST['wcb2b_group_gateways']);
        }
    });

    /* PRODUCT CATEGORIES VISIBILITY BY GROUP */

    // Create visibility term meta if not exists
    add_action('updated_option', function ($option, $old_value, $value) {
        if ($option !== 'wcb2b_product_cat_visibility') {
            return;
        }
        if ($value !== 'yes') {
            return;
        }

        if ($terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'bypass' => 1))) {
            // Get all groups and add -1 for guest group
            $wcb2b_groups = array_merge(array(-1), wp_list_pluck(wcb2b_get_groups()->posts, 'ID'));
            $wcb2b_groups = array_filter(array_unique($wcb2b_groups));

            // Add visibility to all groups (if term meta not exists; if exists, skip to prevent override)
            foreach ($terms as $term) {
                add_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', $wcb2b_groups, true);
            }
        }
    }, 10, 3);

    // On save group (only add new) add to all terms visibility
    add_action('publish_wcb2b_group', function ($post_id, $post) {
        if ($terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'bypass' => 1))) {
            // Update visible groups
            foreach ($terms as $term) {
                if (!$wcb2b_group_visibility = get_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', true)) {
                    // Fix if term meta is empty
                    $wcb2b_group_visibility = array();
                }
                array_push($wcb2b_group_visibility, $post_id);
                $wcb2b_group_visibility = array_filter(array_unique($wcb2b_group_visibility));

                // Add created group id to term meta
                update_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', $wcb2b_group_visibility);
            }
        }
    }, 10, 2);

    // On delete group, remove visibility from all terms
    add_action('trash_wcb2b_group', function ($post_id) {
        if ($terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'bypass' => 1))) {
            // Update visible groups
            foreach ($terms as $term) {
                if (!$wcb2b_group_visibility = get_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', wcb2b_get_groups()->posts, true)) {
                    // Fix if term meta is empty
                    $wcb2b_group_visibility = array();
                }
                if ($key = array_search($post_id, (array)$wcb2b_group_visibility) !== false) {
                    unset($wcb2b_group_visibility[$key]);
                }
                $wcb2b_group_visibility = array_filter(array_unique($wcb2b_group_visibility));

                // Update groups id to term meta
                update_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', $wcb2b_group_visibility);
            }
        }
    });

    if (get_option('wcb2b_product_cat_visibility') === 'yes') {

        // Return visible product categories ids, filtered by user (if logged in) or empty
        function wcb2b_get_allowed_terms()
        {
            return WC()->wcb2b_allowed_terms;
        }

        // Return visible products ids (belonging visible product categories), filtered by user (if logged in) or empty
        function wcb2b_get_allowed_products()
        {
            return WC()->wcb2b_allowed_products;
        }

        // Set visible terms for current customer group in WooCommerce global class
        function wcb2b_set_allowed_terms()
        {
            // Guests default
            $wcb2b_user_group = -1;
            $ids = array();

            if (is_user_logged_in()) {
                $wcb2b_user_group = get_the_author_meta('wcb2b_group', get_current_user_id(), true);

                // Fallback if group not exists
                if (false === get_post_status($wcb2b_user_group)) {
                    $wcb2b_user_group = -1;
                }
            }

            // If no terms, return empty array
            if (!$terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'bypass' => 1))) {
                return $ids;
            }

            // Check for each term if can be visible
            foreach ($terms as $term) {
                if (!$wcb2b_group_visibility = get_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', true)) {
                    // Fix if term meta is empty
                    $wcb2b_group_visibility = array();
                }
                if (in_array($wcb2b_user_group, (array)$wcb2b_group_visibility)) {
                    $ids[] = $term->term_id;
                }
            }
            return $ids;
        }

        // Set visible products for current customer group in WooCommerce global class
        function wcb2b_set_allowed_products()
        {
            $args = array(
                'post_type' => 'product',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => wcb2b_get_allowed_terms(),
                        'operator' => 'IN',
                        'include_children' => false
                    )
                )
            );
            $products = new WP_Query($args);
            return (array)$products->posts;
        }

        // Add metaboxes for product categories to add groups visibility (new category)
        add_action('product_cat_add_form_fields', function () {
            // Only ShopManager can edit customer group
            if (!current_user_can('manage_woocommerce')) {
                return;
            }

            $wcb2b_groups = wcb2b_get_groups();
            ?>
            <div class="form-field term-display-type-wrap">
                <table class="form-table wc_gateways widefat">
                    <thead>
                    <tr>
                        <th colspan="2"><?php _e('Group access', 'woocommerce-b2b'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width="1%"><input type="checkbox" name="wcb2b_group_visibility[]"
                                              id="wcb2b_group_visibility" value="-1" checked="checked"/></td>
                        <td><?php _e('Guests', 'woocommerce-b2b'); ?></td>
                    </tr>

                    <?php if ($wcb2b_groups->have_posts()) : ?>
                        <?php while ($wcb2b_groups->have_posts()) : $wcb2b_groups->the_post(); ?>
                            <tr>
                                <td><input type="checkbox" name="wcb2b_group_visibility[]"
                                           id="wcb2b_group_visibility_<?php the_ID(); ?>" value="<?php the_ID(); ?>"
                                           checked="checked"/></td>
                                <td><?php the_title(); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif;
                    wp_reset_postdata(); ?>

                    </tbody>
                </table>
                <p><?php _e('Select the groups you want to be able to view this category', 'woocommerce-b2b'); ?></p>
            </div>
            <?php
        });

        // Add metaboxes for product categories to add groups visibility (edit category)
        add_action('product_cat_edit_form_fields', function ($term) {
            // Only ShopManager can edit customer group
            if (!current_user_can('manage_woocommerce')) {
                return;
            }

            $wcb2b_groups = wcb2b_get_groups();
            $wcb2b_group_visibility = (array)get_woocommerce_term_meta($term->term_id, 'wcb2b_group_visibility', true);
            ?>
            <tr class="form-field">
                <th scope="row" valign="top"><label><?php _e('Group access', 'woocommerce-b2b'); ?></label></th>
                <td>
                    <table class="form-table wc_gateways widefat">
                        <tbody>
                        <tr>
                            <td width="1%"><input type="checkbox" name="wcb2b_group_visibility[]"
                                                  id="wcb2b_group_visibility"
                                                  value="-1" <?php checked(in_array(-1, $wcb2b_group_visibility), true); ?> />
                            </td>
                            <td><?php _e('Guests', 'woocommerce-b2b'); ?></td>
                        </tr>

                        <?php if ($wcb2b_groups->have_posts()) : ?>
                            <?php while ($wcb2b_groups->have_posts()) : $wcb2b_groups->the_post(); ?>
                                <tr>
                                    <td><input type="checkbox" name="wcb2b_group_visibility[]"
                                               id="wcb2b_group_visibility_<?php the_ID(); ?>"
                                               value="<?php the_ID(); ?>" <?php checked(in_array(get_the_ID(), $wcb2b_group_visibility), true); ?> />
                                    </td>
                                    <td><?php the_title(); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif;
                        wp_reset_postdata(); ?>
                        </tbody>
                    </table>
                    <p class="description"><?php _e('Select the groups you want to be able to view this category', 'woocommerce-b2b'); ?></p>
                </td>
            </tr>
            <?php
        }, 10);

        // Save groups visibility when save product categories
        add_action('created_term', 'wcb2b_save_product_cat_fields', 10, 3);
        add_action('edit_term', 'wcb2b_save_product_cat_fields', 10, 3);
        function wcb2b_save_product_cat_fields($term_id, $tt_id, $taxonomy)
        {
            if ('product_cat' === $taxonomy) {
                $wcb2b_group_visibility = false;
                if (isset($_POST['wcb2b_group_visibility'])) {
                    // Sanitize values
                    $wcb2b_group_visibility = array_map('intval', $_POST['wcb2b_group_visibility']);
                }
                update_woocommerce_term_meta($term_id, 'wcb2b_group_visibility', $wcb2b_group_visibility);
            }
        }

        add_action('init', function () {
            // ShopManager can view all
            if (current_user_can('manage_woocommerce')) {
                return;
            }

            WC()->wcb2b_allowed_terms = wcb2b_set_allowed_terms();
            WC()->wcb2b_allowed_products = wcb2b_set_allowed_products();

            // Add a bypass argument to use it in global product categories filter (used to redirect page if hidden)
            add_filter('wp_get_object_terms_args', function ($args) {
                $args['bypass'] = 1;
                return $args;
            }, 10, 1);

            // Remove products from WooCommerce products widgets and shortcodes if has restricted category
            add_filter('woocommerce_products_widget_query_args', 'wcb2b_widget_allow_product_cat');
            add_filter('woocommerce_recently_viewed_products_widget_query_args', 'wcb2b_widget_allow_product_cat');
            function wcb2b_widget_allow_product_cat($args)
            {
                $args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => wcb2b_get_allowed_terms(),
                    'operator' => 'IN',
                    'include_children' => false
                );
                return $args;
            }

            // Display only products belonging visibile category for current user group
            add_filter('woocommerce_product_query_tax_query', function ($query) {
                $query[] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => wcb2b_get_allowed_terms(),
                    'operator' => 'IN',
                    'include_children' => false
                );
                return $query;
            });

            // Exclude globally not allowed product categories from displaying
            add_filter('get_terms_args', function ($args, $taxonomies) {
                if (!in_array('product_cat', $args['taxonomy']) || isset($args['bypass'])) {
                    return $args;
                }

                $terms = wcb2b_get_allowed_terms();
                if (empty($args['include'])) {
                    $args['include'] = $terms;
                }
                $args['include'] = !empty($terms) ? array_intersect($args['include'], $terms) : -1;

                return $args;
            }, 10, 2);

            // Exclude not allowed product categories and products
            add_filter('wp_get_nav_menu_items', function ($items, $menu, $args) {
                $products = wcb2b_get_allowed_products();
                $terms = wcb2b_get_allowed_terms();

                foreach ($items as $key => $item) {
                    if (
                        ($item->object == 'product' && !$products && !in_array($item->object_id, $products)) ||
                        ($item->object == 'product_cat' && !in_array($item->object_id, $terms))
                    ) {
                        unset($items[$key]);
                    }
                }
                return $items;
            }, 10, 3);

            // Redirect to choosed page if product has restricted category
            add_action('template_redirect', function () {
                // ShopManager can view all
                if (current_user_can('manage_woocommerce')) {
                    return;
                }

                global $wp_query;
                $allowed_terms = wcb2b_get_allowed_terms();

                // If is a product page
                if (is_product()) {
                    $skip = empty($allowed_terms) || has_term($allowed_terms, 'product_cat', $wp_query->post->ID);
                } elseif (is_product_category()) {
                    $product_cat = get_queried_object();
                    $skip = in_array($product_cat->term_id, $allowed_terms);
                } else {
                    $skip = true;
                }

                if ($skip) {
                    return;
                }

                $redirect = get_option('wcb2b_redirect_not_allowed', 'null');
                switch ($redirect) {
                    case 'null' :
                        break;
                    case '0' :
                        $wp_query->set_404();
                        status_header(404);
                        break;
                    default :
                        wp_safe_redirect(get_permalink($redirect), 302);
                        exit;
                        break;
                }
            });

            // Exclude not allowed product categories and products in related
            add_filter('woocommerce_product_related_posts_query', function ($query) {
                $query['where'] .= ' AND p.ID IN ( ' . implode(',', wcb2b_get_allowed_products()) . ' )';
                return $query;
            });
        });

        // Override WooCommerce function to remove hidden products from up-sells, cross-sells, related
        function woocommerce_upsell_display($limit = '-1', $columns = 4, $orderby = 'rand', $order = 'desc')
        {
            global $product;

            if (!$product) {
                return;
            }

            // Handle the legacy filter which controlled posts per page etc.
            $args = apply_filters('woocommerce_upsell_display_args', array(
                'posts_per_page' => $limit,
                'orderby' => $orderby,
                'columns' => $columns,
            ));
            wc_set_loop_prop('name', 'up-sells');
            wc_set_loop_prop('columns', apply_filters('woocommerce_upsells_columns', isset($args['columns']) ? $args['columns'] : $columns));

            $orderby = apply_filters('woocommerce_upsells_orderby', isset($args['orderby']) ? $args['orderby'] : $orderby);
            $limit = apply_filters('woocommerce_upsells_total', isset($args['posts_per_page']) ? $args['posts_per_page'] : $limit);

            // Get visible upsells then sort them at random, then limit result set.
            if (!$allowed_products = wcb2b_get_allowed_products()) {
                $allowed_products = array();
            }
            if (!is_admin() && !(is_user_logged_in() && in_array('administrator', get_userdata(get_current_user_id())->roles))) {
                $allowed_products = array_intersect($product->get_upsell_ids(), $allowed_products);
            }

            $upsells = wc_products_array_orderby(array_filter(array_map('wc_get_product', $allowed_products), 'wc_products_array_filter_visible'), $orderby, $order);
            $upsells = $limit > 0 ? array_slice($upsells, 0, $limit) : $upsells;

            wc_get_template('single-product/up-sells.php', array(
                'upsells' => $upsells,
                // Not used now, but used in previous version of up-sells.php.
                'posts_per_page' => $limit,
                'orderby' => $orderby,
                'columns' => $columns,
            ));
        }

        function woocommerce_cross_sell_display($limit = 2, $columns = 2, $orderby = 'rand', $order = 'desc')
        {
            if (is_checkout()) {
                return;
            }

            // Get visible cross sells then sort them at random.
            if (!$allowed_products = wcb2b_get_allowed_products()) {
                $allowed_products = array();
            }
            if (!is_admin() && !(is_user_logged_in() && in_array('administrator', get_userdata(get_current_user_id())->roles))) {
                $allowed_products = array_intersect(WC()->cart->get_cross_sells(), $allowed_products);
            }
            $cross_sells = array_filter(array_map('wc_get_product', $allowed_products), 'wc_products_array_filter_visible');

            wc_set_loop_prop('name', 'cross-sells');
            wc_set_loop_prop('columns', apply_filters('woocommerce_cross_sells_columns', $columns));

            // Handle orderby and limit results.
            $orderby = apply_filters('woocommerce_cross_sells_orderby', $orderby);
            $order = apply_filters('woocommerce_cross_sells_order', $order);
            $cross_sells = wc_products_array_orderby($cross_sells, $orderby, $order);
            $limit = apply_filters('woocommerce_cross_sells_total', $limit);
            $cross_sells = $limit > 0 ? array_slice($cross_sells, 0, $limit) : $cross_sells;

            wc_get_template('cart/cross-sells.php', array(
                'cross_sells' => $cross_sells,

                // Not used now, but used in previous version of up-sells.php.
                'posts_per_page' => $limit,
                'orderby' => $orderby,
                'columns' => $columns,
            ));
        }
    }

    /* MODERATE REGISTRATION */

    if (get_option('wcb2b_moderate_customer_registration') === 'yes') {

        // Include email classes
        add_filter('woocommerce_email_classes', function ($emails) {
            if (!array_key_exists('WC_Email_Status_Notification', $emails)) {
                $emails['WC_Email_Status_Notification'] = include(plugin_dir_path(__FILE__) . 'includes/classes/class-wc-email-status-notification.php');
            }

            return $emails;
        });

        // On registration, prevent autologin
        add_filter('woocommerce_registration_auth_new_customer', '__return_false');
        add_action('woocommerce_registration_redirect', function () {
            return get_permalink(get_option('woocommerce_myaccount_page_id')) . '?waiting=true';
        }, 2);

        // Display message in login form
        add_action('woocommerce_before_customer_login_form', function () {
            if (isset($_REQUEST['waiting'])) {
                echo '<div class="woocommerce-message">' . apply_filters('wcb2b_waiting_approvation', __('Your account is now under review. You will receive an email as soon as it is activated.', 'woocommerce-b2b')) . '</div>';
            }
        }, 2);

        // Force customers to login with email instead of username and check for user status
        add_filter('woocommerce_login_credentials', function ($credentials) {
            $username = $credentials['user_login'];
            if (!$user = get_user_by('email', $username)) {
                $user = get_user_by('login', $username);
            }

            $wcb2b_status = get_option('wcb2b_moderate_customer_registration') === 'yes' ? get_the_author_meta('wcb2b_status', $user->ID, true) : 1;
            if (0 == (int)$wcb2b_status) {
                $credentials['user_login'] = '';
                add_filter('login_errors', function () {
                    return __('Your account is now under review. You will receive an email as soon as it is activated.', 'woocommerce-b2b');
                });
            }
            return $credentials;
        });

        // Add message into new account email to inform customers to wait activation
        add_action('woocommerce_email_footer', function ($email) {
            // Customize new account email
            if (is_object($email) && $email->id == 'customer_new_account') {
                echo '<p>' . apply_filters('wcb2b_new_account_email', __('We are checking your account, please wait for the activation confirmation email before trying to login', 'woocommerce-b2b')) . '</p>';
            }
        });

        // Register scripts & styles
        add_action('admin_enqueue_scripts', function ($hook) {
            // Only ShopManager can manage activation
            if (!current_user_can('manage_woocommerce')) {
                return;
            }

            // Hook only on requested page
            if (strpos($hook, 'users.php') === false) {
                return;
            }

            // Support for minified versions
            $minified = SCRIPT_DEBUG === false ? '.min' : false;

            wp_enqueue_style('wcb2b_user_status', plugins_url('resources/css/wcb2b_user_status' . $minified . '.css', __FILE__), array('woocommerce_admin_styles'), '1.0', 'all');
            wp_enqueue_script('wcb2b_user_status', plugins_url('resources/js/wcb2b_user_status' . $minified . '.js', __FILE__), false, '1.0', true);
            wp_localize_script('wcb2b_user_status', 'wcb2b_statuses_parameters', array(
                'wcb2b_statuses' => json_encode(array(
                    0 => __('Inactive', 'woocommerce-b2b'),
                    1 => __('Active', 'woocommerce-b2b')
                ))
            ));
        });

        // Add field to user edit page
        add_action('show_user_profile', 'wcb2b_user_profile_status');
        add_action('edit_user_profile', 'wcb2b_user_profile_status');
        function wcb2b_user_profile_status($user)
        {
            // Only ShopManager can edit customer status
            if (!current_user_can('manage_woocommerce')) {
                return;
            }

            // If user isn't a customer, skip. Only customers have group field
            if (!in_array('customer', get_userdata($user->ID)->roles)) {
                return;
            }

            $wcb2b_statuses = array(
                0 => __('Inactive', 'woocommerce-b2b'),
                1 => __('Active', 'woocommerce-b2b')
            );
            ?>
            <h3><?php _e('User status', 'woocommerce-b2b'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="wcb2b_status"><?php _e('Status', 'woocommerce-b2b'); ?></label></th>
                    <td>
                        <select name="wcb2b_status" id="wcb2b_status" class="regular-text">
                            <?php foreach ($wcb2b_statuses as $key => $wcb2b_status) : ?>
                                <option value="<?php echo $key; ?>" <?php selected(get_the_author_meta('wcb2b_status', $user->ID, true), $key); ?> ><?php echo $wcb2b_status; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br/>
                        <span class="description"><?php _e('Please select user status to approve registration and enable purchases.', 'woocommerce-b2b'); ?></span>
                    </td>
                </tr>
            </table>
            <?php
        }

        // Update user status
        add_action('personal_options_update', 'wcb2b_user_update_status');
        add_action('edit_user_profile_update', 'wcb2b_user_update_status');
        function wcb2b_user_update_status($user_id)
        {
            // Only ShopManager can edit customer status
            if (!current_user_can('manage_woocommerce')) {
                return false;
            }

            // If user isn't a customer, skip
            if (!in_array('customer', get_userdata($user_id)->roles)) {
                return false;
            }

            $current_status = get_the_author_meta('wcb2b_status', $user_id, true);

            update_user_meta($user_id, 'wcb2b_status', intval($_POST['wcb2b_status']));

            // If status is enabled, send approvation confirmation to customer
            if (intval($_POST['wcb2b_status']) && apply_filters('wcb2b_send_activation_notification', true)) {
                if ($current_status == 0) {
                    $emails = WC_Emails::instance()->emails;
                    if (!array_key_exists('WC_Email_Status_Notification', $emails)) {
                        $emails['WC_Email_Status_Notification'] = include(plugin_dir_path(__FILE__) . 'includes/classes/class-wc-email-status-notification.php');
                    }
                    do_action('wcb2b_status_notification', $user_id);
                }
            }
        }

        // Add new status column
        add_filter('manage_users_columns', function ($columns) {
            $columns['wcb2b_status'] = __('Status', 'woocommerce-b2b');
            return $columns;
        });

        // Retrieve user status column value
        add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {
            // If user isn't a customer, return empty
            if (!in_array('customer', get_userdata($user_id)->roles)) {
                return $value;
            }

            if ($column_name === 'wcb2b_status') {
                return get_the_author_meta('wcb2b_status', $user_id) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>';
            }
            return $value;
        }, 10, 3);

        // Make status column sortable
        add_filter('manage_users_sortable_columns', function ($columns) {
            $columns['wcb2b_status'] = 'wcb2b_status';
            return $columns;
        });

        // Bulk assignment to status
        add_filter('bulk_actions-users', function ($actions) {
            // If admin can manage WooCommerce add actions
            if (!current_user_can('manage_woocommerce')) {
                return $actions;
            }

            $actions['wcb2b-change_status-action'] = __('Change status', 'woocommerce-b2b');
            return $actions;
        });

        // Process new status assignment and status change and return updated users
        add_filter('handle_bulk_actions-users', function ($redirect_url, $action, $user_ids) {
            // Only ShopManager can edit customer status
            if (!current_user_can('manage_woocommerce')) {
                return $redirect_url;
            }

            // Bulk change status handle
            if ($action == 'wcb2b-change_status-action' && isset($_REQUEST['wcb2b_status'])) {
                $wcb2b_status = $_REQUEST['wcb2b_status'];

                // Validate request
                if (!is_numeric($wcb2b_status)) {
                    return $redirect_url;
                }

                foreach ($user_ids as $user_id) {
                    // Skip if user hasn't a customer role
                    if (!in_array('customer', get_userdata($user_id)->roles)) {
                        continue;
                    }

                    $current_status = get_the_author_meta('wcb2b_status', $user_id, true);

                    // Update status
                    update_user_meta($user_id, 'wcb2b_status', intval($wcb2b_status));

                    // If status is enabled, send approvation confirmation to customer
                    if (intval($wcb2b_status) && apply_filters('wcb2b_send_activation_notification', true)) {
                        if ($current_status == 0) {
                            $emails = WC_Emails::instance()->emails;
                            if (!array_key_exists('WC_Email_Status_Notification', $emails)) {
                                $emails['WC_Email_Status_Notification'] = include(plugin_dir_path(__FILE__) . 'includes/classes/class-wc-email-status-notification.php');
                            }
                            do_action('wcb2b_status_notification', $user_id);
                        }
                    }
                }
            }
            return $redirect_url;
        }, 10, 3);

        // Add filter by status
        add_filter('wcb2b_users_extra_tablenav', function ($filters) {
            $filters .= '<select name="wcb2b_status_filter" id="wcb2b_status_filter">';
            $filters .= '<option value="">' . esc_html__('Filter by status', 'woocommerce-b2b') . '</option>';


            // Retrieve statuses
            $wcb2b_statuses = array(
                0 => __('Inactive', 'woocommerce-b2b'),
                1 => __('Active', 'woocommerce-b2b')
            );

            foreach ($wcb2b_statuses as $wcb2b_status => $wcb2b_status_label) {
                $filters .= sprintf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    $wcb2b_status,
                    (isset($_GET['wcb2b_status_filter']) && $_GET['wcb2b_status_filter'] != '' && intval($_GET['wcb2b_status_filter']) === $wcb2b_status ? ' selected="selected"' : ''),
                    $wcb2b_status_label
                );
            }
            $filters .= '</select>';

            return $filters;
        });

    }

    /* EXPORT TOOLS */

    // Add customer exporter to WordPress tools
    add_filter('export_filters', function () {
        ?>
        <fieldset>
            <p>
                <label>
                    <input type="radio" name="content" value="customers"><?php _e('Customers', 'woocommerce-b2b'); ?>
                </label>
            </p>
            <p class="description"><?php _e('In CSV format for statistics purposes', 'woocommerce-b2b'); ?></p>
        </fieldset>
        <?php
    });

    // Export customers
    add_filter('export_wp', function ($args) {
        if ('customers' == $args['content']) {
            $status = array(
                0 => __('Inactive', 'woocommerce-b2b'),
                1 => __('Active', 'woocommerce-b2b')
            );
            // Requested fields
            $fields = array(
                'ID',
                'user_login',
                'user_email',
                'user_registered',
                'first_name',
                'last_name',
                'billing_first_name',
                'billing_last_name',
                'billing_company',
                'billing_vat',
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_postcode',
                'billing_country',
                'billing_state',
                'billing_phone',
                'billing_email',
                'shipping_first_name',
                'shipping_last_name',
                'shipping_company',
                'shipping_address_1',
                'shipping_address_2',
                'shipping_city',
                'shipping_postcode',
                'shipping_country',
                'shipping_state',
                'wcb2b_group',
                'wcb2b_status'
            );

            // Get users by role (customer)
            $customers = get_users(array(
                'role' => 'customer',
                'fields' => array(
                    'ID',
                    'user_login',
                    'user_email',
                    'user_registered'
                )
            ));

            // If any customer is retrieved, skip
            if (!$customers) {
                $referer = add_query_arg('error', 'empty', wp_get_referer());
                wp_redirect($referer);
                exit;
            }

            // Prepare headers to download CSV file
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . sanitize_key(get_bloginfo('name')) . '-customers-' . date('YmdHis') . '.csv');
            header('Content-Type: text/csv; charset=' . get_option('blog_charset'), true);

            echo strtoupper(str_replace('_', ' ', implode(';', $fields))) . "\n";
            foreach ($customers as $customer) {
                $data = array_flip($fields);
                $data = array_fill_keys($fields, null);
                $data = array_merge($data, (array)$customer, wp_list_pluck(get_user_meta($customer->ID), 0));
                $data = array_intersect_key($data, array_flip($fields));

                $data['wcb2b_group'] = $data['wcb2b_group'] ? get_post($data['wcb2b_group'])->post_title : '';
                $data['wcb2b_status'] = $status[(int)$data['wcb2b_status']];
                $data['user_registered'] = date_i18n(get_option('date_format'), strtotime($data['user_registered']));

                echo implode(';', $data) . "\n";
            }
            exit;
        }
    });

}

// Update database for new groups management
if (get_option('wcb2b_groups', false)) {
    if (!empty($_GET['do_update_wcb2b'])) {
        add_action('admin_init', function () {
            if (!$groups = get_option('wcb2b_groups', false)) {
                delete_option('wcb2b_groups');
                return;
            }

            $customers = get_users(array(
                'role' => 'customer',
                'fields' => array('ID')
            ));

            foreach ($groups as $group) {
                $group_id = post_exists($group['name']);
                if (!$group_id) {
                    $group_id = wp_insert_post(array(
                        'post_type' => 'wcb2b_group',
                        'post_title' => $group['name'],
                        'post_status' => 'publish'
                    ));
                }

                if (!$group_id) {
                    continue;
                }

                add_post_meta($group_id, 'wcb2b_group_discount', $group['discount']);
                foreach ($customers as $customer) {
                    if (get_the_author_meta('wcb2b_groups', $customer->ID, true) == $group['id']) {
                        update_user_meta($customer->ID, 'wcb2b_group', $group_id);
                        delete_user_meta($customer->ID, 'wcb2b_groups');
                    }
                }
            }
            delete_option('wcb2b_groups');

            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-success">
                    <p><?php _e('Database updated!', 'woocommerce-b2b'); ?></p>
                </div>
                <?php
            });
        });
    } else {
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-info">
                <p><strong>WooCommerce B2B</strong>
                    - <?php _e('We need to update your database to the latest version to preserve all your data.', 'woocommerce-b2b'); ?>
                </p>
                <p><a href="<?php echo esc_url(add_query_arg('do_update_wcb2b', 'true')); ?>"
                      class="button-primary"><?php _e('Run the updater', 'woocommerce-b2b'); ?></a></p>
            </div>
            <?php
        });
    }
}