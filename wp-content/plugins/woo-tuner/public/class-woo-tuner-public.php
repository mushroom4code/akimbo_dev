<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://volkov.co.il/
 * @since      1.0.0
 *
 * @package    Woocommerce_Tuner
 * @subpackage Woocommerce_Tuner/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Tuner
 * @subpackage Woocommerce_Tuner/public
 * @author     Alexander Volkov <vol4ikman@gmail.com>
 */
class Woo_Tuner_Public {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		add_action('init', array(&$this, 'woo_tuner_setup'));
		add_action('init', array(&$this, 'order_single_product_widgets'));
	}

	public function order_single_product_widgets() {
		/**
		* woocommerce_single_product_summary hook.
		*
		* @hooked woocommerce_template_single_title - 5
		* @hooked woocommerce_template_single_rating - 10
		* @hooked woocommerce_template_single_price - 10
		* @hooked woocommerce_template_single_excerpt - 20
		* @hooked woocommerce_template_single_add_to_cart - 30
		* @hooked woocommerce_template_single_meta - 40
		* @hooked woocommerce_template_single_sharing - 50
		* @hooked WC_Structured_Data::generate_product_data() - 60
		*/
		if(get_option('woocommerce_single_product_summary_widgets_order')) {
			$start_priority = 5;
			$ordered_widgets = get_option('woocommerce_single_product_summary_widgets_order');
			$ordered_widgets = unserialize($ordered_widgets);
			foreach($ordered_widgets as $key=>$hook){
				switch ($hook) {
				    case 'woocommerce_template_single_title':
						if(!get_option('woo_tuner_remove_product_title')) {
							remove_action('woocommerce_single_product_summary',$hook, 5, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
				    case 'woocommerce_template_single_rating':
						if(!get_option('woo_tuner_remove_single_rating')) {
							remove_action('woocommerce_single_product_summary',$hook, 10, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
				    case 'woocommerce_template_single_price':
						if(!get_option('woo_tuner_remove_single_price')) {
							remove_action('woocommerce_single_product_summary',$hook, 10, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
					case 'woocommerce_template_single_excerpt':
						if(!get_option('woo_tuner_remove_single_excerpt')){
							remove_action('woocommerce_single_product_summary',$hook, 20, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
					case 'woocommerce_template_single_add_to_cart':
						if(!get_option('woo_tuner_remove_single_add_to_cart')){
							remove_action('woocommerce_single_product_summary',$hook, 30, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
					case 'woocommerce_template_single_meta':
						if(!get_option('woo_tuner_remove_single_meta')){
							remove_action('woocommerce_single_product_summary',$hook, 40, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
					case 'woocommerce_template_single_sharing':
						if(!get_option('woo_tuner_remove_single_sharing')) {
							remove_action('woocommerce_single_product_summary',$hook, 50, 0);
							add_action('woocommerce_single_product_summary',$hook, $start_priority);
						}
				        break;
				}

				$start_priority+=5;
			}
		}
	}

	public function woo_tuner_setup(){
		/************************
		*** Global Settings 
		*************************/
		//Remove Breadcrumbs
		$woo_tuner_remove_breadcrumbs = get_option('woo_tuner_remove_breadcrumbs');
		if(isset($woo_tuner_remove_breadcrumbs) && $woo_tuner_remove_breadcrumbs){
			remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20,0);
		}
		$woo_tuner_disable_stylesheets = get_option('woo_tuner_disable_stylesheets');
		if(isset($woo_tuner_disable_stylesheets) && $woo_tuner_disable_stylesheets){
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		}

		//Remove Single Product Title
		$woo_tuner_remove_product_title = get_option('woo_tuner_remove_product_title');
		if(isset($woo_tuner_remove_product_title) && $woo_tuner_remove_product_title){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_title',5,0);
		}
		//Remove Single Rating
		$woo_tuner_remove_single_rating = get_option('woo_tuner_remove_single_rating');
		if(isset($woo_tuner_remove_single_rating) && $woo_tuner_remove_single_rating){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_rating',10,0);
		}
		//Remove Price
		$woo_tuner_remove_single_price = get_option('woo_tuner_remove_single_price');
		if(isset($woo_tuner_remove_single_price) && $woo_tuner_remove_single_price){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10,0);
		}
		//Remove Single Product Excerpt
		$woo_tuner_remove_single_excerpt = get_option('woo_tuner_remove_single_excerpt');
		if(isset($woo_tuner_remove_single_excerpt) && $woo_tuner_remove_single_excerpt){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt',20,0);
		}
		//Remove Single Product Add to Cart
		$woo_tuner_remove_single_add_to_cart = get_option('woo_tuner_remove_single_add_to_cart');
		if(isset($woo_tuner_remove_single_add_to_cart) && $woo_tuner_remove_single_add_to_cart){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',30,0);
		}
		//Remove Single Product Meta
		$woo_tuner_remove_single_meta = get_option('woo_tuner_remove_single_meta');
		if(isset($woo_tuner_remove_single_meta) && $woo_tuner_remove_single_meta){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40,0);
		}
		//Remove Single Product Sharing
		$woo_tuner_remove_single_sharing = get_option('woo_tuner_remove_single_sharing');
		if(isset($woo_tuner_remove_single_sharing) && $woo_tuner_remove_single_sharing){
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_sharing',50,0);
		}
		//Remove Single Product Sale Flash
		$woo_tuner_remove_single_sale_flash = get_option('woo_tuner_remove_single_sale_flash');
		if(isset($woo_tuner_remove_single_sale_flash) && $woo_tuner_remove_single_sale_flash){
			remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash',10,0);
		}
		//Remove Single Product Images
		$woo_tuner_remove_single_images = get_option('woo_tuner_remove_single_images');
		if(isset($woo_tuner_remove_single_images) && $woo_tuner_remove_single_images){
			remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_images',20,0);
		}
		//Remove Single Product Data Tabs
		$woo_tuner_remove_single_data_tabs = get_option('woo_tuner_remove_single_data_tabs');
		if(isset($woo_tuner_remove_single_data_tabs) && $woo_tuner_remove_single_data_tabs){
			remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10,0);
		}
		//Remove Single Product Upsell
		$woo_tuner_remove_single_upsell = get_option('woo_tuner_remove_single_upsell');
		if(isset($woo_tuner_remove_single_upsell) && $woo_tuner_remove_single_upsell){
			remove_action('woocommerce_after_single_product_summary','woocommerce_upsell_display',15,0);
		}
		//Remove Single Related Products
		$woo_tuner_remove_related_products = get_option('woo_tuner_remove_related_products');
		if(isset($woo_tuner_remove_related_products) && $woo_tuner_remove_related_products){
			remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20,0);
		}
		//Remove Taxonomy Archive Description
		$woo_tuner_remove_taxonomy_archive_description = get_option('woo_tuner_remove_taxonomy_archive_description');
		if(isset($woo_tuner_remove_taxonomy_archive_description) && $woo_tuner_remove_taxonomy_archive_description){
			remove_action('woocommerce_archive_description','woocommerce_taxonomy_archive_description',10,0);
		}
		//Remove Product Archive Description
		$woo_tuner_remove_product_archive_description = get_option('woo_tuner_remove_product_archive_description');
		if(isset($woo_tuner_remove_product_archive_description) && $woo_tuner_remove_product_archive_description){
			remove_action('woocommerce_archive_description','woocommerce_product_archive_description',10,0);
		}
		//Remove Taxonomy Result Count
		$woo_tuner_remove_taxonomy_result_count= get_option('woo_tuner_remove_taxonomy_result_count');
		if(isset($woo_tuner_remove_taxonomy_result_count) && $woo_tuner_remove_taxonomy_result_count){
			remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20,0);
		}
		//Remove Taxonomy Catalog Order
		$woo_tuner_remove_taxonomy_catalog_ordering = get_option('woo_tuner_remove_taxonomy_catalog_ordering');
		if(isset($woo_tuner_remove_taxonomy_catalog_ordering) && $woo_tuner_remove_taxonomy_catalog_ordering){
			remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering',30,0);
		}
		//Remove Taxonomy Pagination
		$woo_tuner_remove_taxonomy_pagination = get_option('woo_tuner_remove_taxonomy_pagination');
		if(isset($woo_tuner_remove_taxonomy_pagination) && $woo_tuner_remove_taxonomy_pagination){
			remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10,0);
		}
		//Remove Product Loop Sale Flash
		$woo_tuner_remove_product_loop_sale_flash = get_option('woo_tuner_remove_product_loop_sale_flash');
		if(isset($woo_tuner_remove_product_loop_sale_flash) && $woo_tuner_remove_product_loop_sale_flash){
			remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash',10,0);
		}
		//Remove Product Loop Thumbnail
		$woo_tuner_remove_product_loop_thumbnail = get_option('woo_tuner_remove_product_loop_thumbnail');
		if(isset($woo_tuner_remove_product_loop_thumbnail) && $woo_tuner_remove_product_loop_thumbnail){
			remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10,0);
		}
		//Remove Product Loop Title
		$woo_tuner_remove_product_loop_title = get_option('woo_tuner_remove_product_loop_title');
		if(isset($woo_tuner_remove_product_loop_title) && $woo_tuner_remove_product_loop_title){
			remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10,0);
		}
		//Remove Product Loop Rating
		$woo_tuner_remove_product_loop_rating = get_option('woo_tuner_remove_product_loop_rating');
		if(isset($woo_tuner_remove_product_loop_rating) && $woo_tuner_remove_product_loop_rating){
			remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5,0);
		}
		//Remove Product Loop Price
		$woo_tuner_remove_product_loop_price = get_option('woo_tuner_remove_product_loop_price');
		if(isset($woo_tuner_remove_product_loop_price) && $woo_tuner_remove_product_loop_price){
			remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price',10,0);
		}
		//Remove Product Loop Add-to-cart
		$woo_tuner_remove_product_loop_add_to_cart = get_option('woo_tuner_remove_product_loop_add_to_cart');
		if(isset($woo_tuner_remove_product_loop_add_to_cart) && $woo_tuner_remove_product_loop_add_to_cart){
			remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10,0);
		}
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-tuner-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-tuner-public.js', array( 'jquery' ), $this->version, false );
	}

}
