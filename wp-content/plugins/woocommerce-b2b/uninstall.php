<?php

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

global $wpdb;

// Delete options and settings
delete_option( 'wcb2b_notice_shown' );
delete_option( 'wcb2b_enable' );
delete_option( 'wcb2b_hide_prices' );
delete_option( 'wcb2b_add_vatnumber' );
delete_option( 'wcb2b_min_purchase_amount' );
delete_option( 'wcb2b_display_min_purchase_cart_message' );
delete_option( 'wcb2b_prevent_checkout_button' );
delete_option( 'wcb2b_moderate_customer_registration' );
delete_option( 'wcb2b_extend_registration_form' );
delete_option( 'wcb2b_registration_notice' );
delete_option( 'wcb2b_show_customer_discount' );
delete_option( 'wcb2b_show_customer_discount_product' );
delete_option( 'wcb2b_product_cat_visibility' );
delete_option( 'wcb2b_redirect_not_allowed' );

// Delete users data.
$wpdb->query( "DELETE FROM {$wpdb->post} WHERE post_type IN ( 'wcb2b_group' );" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ( '_billing_vat', 'wcb2b_step', 'wcb2b_min', 'wcb2b_group_discount' );" );
$wpdb->query( "DELETE FROM {$wpdb->termmeta} WHERE meta_key IN ( 'wcb2b_group_visibility' );" );
$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ( 'wcb2b_group', 'wcb2b_status' );" );

// Clear any cached data that has been removed
flush_rewrite_rules();
wp_cache_flush();