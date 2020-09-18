<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://volkov.co.il/
 * @since      0.0.1
 *
 * @package    Woocommerce_Tuner
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$woo_tuner_options = array(
	"woo_tuner_remove_product_title",
	"woo_tuner_remove_single_rating",
	"woo_tuner_remove_single_price",
	"woo_tuner_remove_single_excerpt",
	"woo_tuner_remove_single_add_to_cart",
	"woo_tuner_remove_single_meta",
	"woo_tuner_remove_single_sharing",
	"woo_tuner_remove_single_sale_flash",
	"woo_tuner_remove_single_images",
	"woo_tuner_remove_single_data_tabs",
	"woo_tuner_remove_single_upsell",
	"woo_tuner_remove_related_products",
	"woo_tuner_remove_breadcrumbs",
	"woo_tuner_disable_stylesheets",
	"woo_tuner_remove_taxonomy_archive_description",
	"woo_tuner_remove_product_archive_description",
	"woo_tuner_remove_taxonomy_result_count",
	"woo_tuner_remove_taxonomy_catalog_ordering",
	"woo_tuner_remove_taxonomy_pagination",
	"woo_tuner_remove_product_loop_sale_flash",
	"woo_tuner_remove_product_loop_thumbnail",
	"woo_tuner_remove_product_loop_title",
	"woo_tuner_remove_product_loop_rating",
	"woo_tuner_remove_product_loop_price",
	"woo_tuner_remove_product_loop_add_to_cart"
);
foreach($woo_tuner_options as $woo_tuner_option){
	delete_option( $woo_tuner_option );
}
