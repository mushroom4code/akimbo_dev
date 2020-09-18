<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://volkov.co.il/
 * @since      0.0.1
 *
 * @package    Woocommerce_Tuner
 * @subpackage Woocommerce_Tuner/admin
 */

/**
 *
 * @package    Woocommerce_Tuner
 * @subpackage Woocommerce_Tuner/admin
 * @author     Alexander Volkov <vol4ikman@gmail.com>
 */
class Woo_Tuner_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('init', array(&$this, 'init'));

	}

	//Init admin functions
	public function init(){
		add_action( 'admin_menu', array($this, 'woo_tuner_admin_menu') );
		add_action( 'wp_ajax_update_single_product_widgets', array($this, 'update_single_product_widgets') );
	}

	public function woo_tuner_admin_menu(){
		add_menu_page(
			__("WooTuner","woo-tuner"),
			__("WooTuner","woo-tuner"),
			'manage_options',
			'woo-tuner-settings',
			array($this, 'render_woo_tuner_settings_page')
		);
		add_submenu_page(
			'woo-tuner-settings',
			__("Order","woo-tuner"),
			__("Order","woo-tuner"),
			'manage_options',
			'woo-tuner-order-widgets',
			array($this, 'render_woo_tuner_order_widgets')
		);
	}

	public function render_woo_tuner_settings_page() {
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		if( isset($_POST['woo_tuner_hidden']) && $_POST['woo_tuner_hidden'] == 'Y' ) {

			/*********** Global Settings *************/
			//Remove Breadcrumbs
			$woo_tuner_remove_breadcrumbs = isset($_POST['woo_tuner_remove_breadcrumbs']) ? 1 : 0;
			update_option('woo_tuner_remove_breadcrumbs',$woo_tuner_remove_breadcrumbs);
			//Disable Woocommerce stylesheets
			$woo_tuner_disable_stylesheets = isset($_POST['woo_tuner_disable_stylesheets']) ? 1 : 0;
			update_option('woo_tuner_disable_stylesheets',$woo_tuner_disable_stylesheets);

			//Remove Single product title
			$woo_tuner_remove_product_title = isset($_POST['woo_tuner_remove_product_title']) ? 1 : 0;
			update_option('woo_tuner_remove_product_title',$woo_tuner_remove_product_title);
			//Remove Single product rating
			$woo_tuner_remove_single_rating = isset($_POST['woo_tuner_remove_single_rating']) ? 1 : 0;
			update_option('woo_tuner_remove_single_rating',$woo_tuner_remove_single_rating);
			//Remove Single product price
			$woo_tuner_remove_single_price = isset($_POST['woo_tuner_remove_single_price']) ? 1 : 0;
			update_option('woo_tuner_remove_single_price',$woo_tuner_remove_single_price);
			//Remove single product excerpt
			$woo_tuner_remove_single_excerpt = isset($_POST['woo_tuner_remove_single_excerpt']) ? 1 : 0;
			update_option('woo_tuner_remove_single_excerpt',$woo_tuner_remove_single_excerpt);
			//Remove single product add to cart
			$woo_tuner_remove_single_add_to_cart = isset($_POST['woo_tuner_remove_single_add_to_cart']) ? 1 : 0;
			update_option('woo_tuner_remove_single_add_to_cart',$woo_tuner_remove_single_add_to_cart);
			//Remove single product Meta
			$woo_tuner_remove_single_meta = isset($_POST['woo_tuner_remove_single_meta']) ? 1 : 0;
			update_option('woo_tuner_remove_single_meta',$woo_tuner_remove_single_meta);
			//Remove single product sharing
			$woo_tuner_remove_single_sharing = isset($_POST['woo_tuner_remove_single_sharing']) ? 1 : 0;
			update_option('woo_tuner_remove_single_sharing',$woo_tuner_remove_single_sharing);
			//Remove single product Sale Flash
			$woo_tuner_remove_single_sale_flash = isset($_POST['woo_tuner_remove_single_sale_flash']) ? 1 : 0;
			update_option('woo_tuner_remove_single_sale_flash',$woo_tuner_remove_single_sale_flash);
			//Remove single product sharing
			$woo_tuner_remove_single_images = isset($_POST['woo_tuner_remove_single_images']) ? 1 : 0;
			update_option('woo_tuner_remove_single_images',$woo_tuner_remove_single_images);
			//Remove single product data tabs
			$woo_tuner_remove_single_data_tabs = isset($_POST['woo_tuner_remove_single_data_tabs']) ? 1 : 0;
			update_option('woo_tuner_remove_single_data_tabs',$woo_tuner_remove_single_data_tabs);
			//Remove single product upsell
			$woo_tuner_remove_single_upsell = isset($_POST['woo_tuner_remove_single_upsell']) ? 1 : 0;
			update_option('woo_tuner_remove_single_upsell',$woo_tuner_remove_single_upsell);
			//Remove related products
			$woo_tuner_remove_related_products = isset($_POST['woo_tuner_remove_related_products']) ? 1 : 0;
			update_option('woo_tuner_remove_related_products',$woo_tuner_remove_related_products);

			//Remove Taxonomy Archive Description
			$woo_tuner_remove_taxonomy_archive_description = isset($_POST['woo_tuner_remove_taxonomy_archive_description']) ? 1 : 0;
			update_option('woo_tuner_remove_taxonomy_archive_description',$woo_tuner_remove_taxonomy_archive_description);
			//Remove Product Archive Description
			$woo_tuner_remove_product_archive_description = isset($_POST['woo_tuner_remove_product_archive_description']) ? 1 : 0;
			update_option('woo_tuner_remove_product_archive_description',$woo_tuner_remove_product_archive_description);
			//Remove Product Taxonomy Result Count
			$woo_tuner_remove_taxonomy_result_count = isset($_POST['woo_tuner_remove_taxonomy_result_count']) ? 1 : 0;
			update_option('woo_tuner_remove_taxonomy_result_count',$woo_tuner_remove_taxonomy_result_count);
			//Remove Product Taxonomy Result Count
			$woo_tuner_remove_taxonomy_catalog_ordering = isset($_POST['woo_tuner_remove_taxonomy_catalog_ordering']) ? 1 : 0;
			update_option('woo_tuner_remove_taxonomy_catalog_ordering',$woo_tuner_remove_taxonomy_catalog_ordering);
			//Remove Product Taxonomy Pagination
			$woo_tuner_remove_taxonomy_pagination = isset($_POST['woo_tuner_remove_taxonomy_pagination']) ? 1 : 0;
			update_option('woo_tuner_remove_taxonomy_pagination',$woo_tuner_remove_taxonomy_pagination);
			//Remove Product Loop Sale Flash
			$woo_tuner_remove_product_loop_sale_flash = isset($_POST['woo_tuner_remove_product_loop_sale_flash']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_sale_flash',$woo_tuner_remove_product_loop_sale_flash);
			//Remove Product Loop Thumbnail
			$woo_tuner_remove_product_loop_thumbnail = isset($_POST['woo_tuner_remove_product_loop_thumbnail']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_thumbnail',$woo_tuner_remove_product_loop_thumbnail);
			//Remove Product Loop Title
			$woo_tuner_remove_product_loop_title = isset($_POST['woo_tuner_remove_product_loop_title']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_title',$woo_tuner_remove_product_loop_title);
			//Remove Product Loop Rating
			$woo_tuner_remove_product_loop_rating = isset($_POST['woo_tuner_remove_product_loop_rating']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_rating',$woo_tuner_remove_product_loop_rating);
			//Remove Product Loop Price
			$woo_tuner_remove_product_loop_price = isset($_POST['woo_tuner_remove_product_loop_price']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_price',$woo_tuner_remove_product_loop_price);
			//Remove Product Loop Add-to-cart button
			$woo_tuner_remove_product_loop_add_to_cart = isset($_POST['woo_tuner_remove_product_loop_add_to_cart']) ? 1 : 0;
			update_option('woo_tuner_remove_product_loop_add_to_cart',$woo_tuner_remove_product_loop_add_to_cart);

		} else {

			//Global settings
			$woo_tuner_remove_breadcrumbs = get_option('woo_tuner_remove_breadcrumbs') ? get_option('woo_tuner_remove_breadcrumbs') : 0;
			$woo_tuner_disable_stylesheets = get_option('woo_tuner_disable_stylesheets') ? get_option('woo_tuner_disable_stylesheets') : 0;

			//Single product
			$woo_tuner_remove_product_title = get_option('woo_tuner_remove_product_title') ? get_option('woo_tuner_remove_product_title') : 0;
			$woo_tuner_remove_single_rating = get_option('woo_tuner_remove_single_rating') ? get_option('woo_tuner_remove_single_rating') : 0;
			$woo_tuner_remove_single_price = get_option('woo_tuner_remove_single_price') ? get_option('woo_tuner_remove_single_price') : 0;
			$woo_tuner_remove_single_excerpt = get_option('woo_tuner_remove_single_excerpt') ? get_option('woo_tuner_remove_single_excerpt') : 0;
			$woo_tuner_remove_single_add_to_cart = get_option('woo_tuner_remove_single_add_to_cart') ? get_option('woo_tuner_remove_single_add_to_cart') : 0;
			$woo_tuner_remove_single_meta = get_option('woo_tuner_remove_single_meta') ? get_option('woo_tuner_remove_single_meta') : 0;
			$woo_tuner_remove_single_sharing = get_option('woo_tuner_remove_single_sharing') ? get_option('woo_tuner_remove_single_sharing') : 0;
			$woo_tuner_remove_single_sale_flash = get_option('woo_tuner_remove_single_sale_flash') ? get_option('woo_tuner_remove_single_sale_flash') : 0;
			$woo_tuner_remove_single_images = get_option('woo_tuner_remove_single_images') ? get_option('woo_tuner_remove_single_images') : 0;
			$woo_tuner_remove_single_data_tabs = get_option('woo_tuner_remove_single_data_tabs') ? get_option('woo_tuner_remove_single_data_tabs') : 0;
			$woo_tuner_remove_single_upsell = get_option('woo_tuner_remove_single_upsell') ? get_option('woo_tuner_remove_single_upsell') : 0;
			$woo_tuner_remove_related_products = get_option('woo_tuner_remove_related_products') ? get_option('woo_tuner_remove_related_products') : 0;

			$woo_tuner_remove_taxonomy_archive_description = get_option('woo_tuner_remove_taxonomy_archive_description') ? get_option('woo_tuner_remove_taxonomy_archive_description') : 0;
			$woo_tuner_remove_product_archive_description = get_option('woo_tuner_remove_product_archive_description') ? get_option('woo_tuner_remove_product_archive_description') : 0;
			$woo_tuner_remove_taxonomy_result_count = get_option('woo_tuner_remove_taxonomy_result_count') ? get_option('woo_tuner_remove_taxonomy_result_count') : 0;
			$woo_tuner_remove_taxonomy_catalog_ordering = get_option('woo_tuner_remove_taxonomy_catalog_ordering') ? get_option('woo_tuner_remove_taxonomy_catalog_ordering') : 0;
			$woo_tuner_remove_taxonomy_pagination = get_option('woo_tuner_remove_taxonomy_pagination') ? get_option('woo_tuner_remove_taxonomy_pagination') : 0;

			$woo_tuner_remove_product_loop_sale_flash = get_option('woo_tuner_remove_product_loop_sale_flash') ? get_option('woo_tuner_remove_product_loop_sale_flash') : 0;
			$woo_tuner_remove_product_loop_thumbnail = get_option('woo_tuner_remove_product_loop_thumbnail') ? get_option('woo_tuner_remove_product_loop_thumbnail') : 0;
			$woo_tuner_remove_product_loop_title = get_option('woo_tuner_remove_product_loop_title') ? get_option('woo_tuner_remove_product_loop_title') : 0;
			$woo_tuner_remove_product_loop_rating = get_option('woo_tuner_remove_product_loop_rating') ? get_option('woo_tuner_remove_product_loop_rating') : 0;
			$woo_tuner_remove_product_loop_price = get_option('woo_tuner_remove_product_loop_price') ? get_option('woo_tuner_remove_product_loop_price') : 0;
			$woo_tuner_remove_product_loop_add_to_cart = get_option('woo_tuner_remove_product_loop_add_to_cart') ? get_option('woo_tuner_remove_product_loop_add_to_cart') : 0;
		} ?>

		<div class="woo-tuner-admin-wrapper">

			<div class="woo-tuner-admin-header">
				<span class="woo-tuner-author">
					<a href="http://volkov.co.il" target="_blank">Created by @vol4ikman</a>
				</span>
				<h1 class="woo-tuner-page-title"><?php _e("Welcome to Woo Tuner!","woo-tuner"); ?></h1>
				<div class="woo-tuner-description">
					<?php _e("Awesome WooCommerce addon! Сustomize woocommerce templates/layouts with no coding skills!","woo-tuner"); ?>
				</div>
			</div>

			<?php if ( class_exists( 'WooCommerce' ) ) : ?>

			<a href="#toggle-templates-name" class="toggle-templates-name button button-default">
				<span class="dashicons dashicons-menu"></span> <?php _e("Show/Hide templates name","woo-tuner"); ?>
			</a>

			<hr />

			<form name="woo_tuner_admin_form" class="clearfix" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

				<input type="hidden" name="woo_tuner_hidden" value="Y">

				<div class="woo_tuner_section">

					<h3 class="woo_tuner_section_title" data-target="woo-tuner-global-settings">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
						<?php _e("Global Settings","woo-tuner"); ?>
						<button class="button button-secondary button-small select-all-checkboxes">
							<?php _e("Check/Uncheck all","woo-tuner"); ?>
						</button>
					</h3>

					<div class="woo_tuner_form_fields_wrapper" id="woo-tuner-global-settings">
						<?php
							$global_checkboxes = array();
							$global_checkboxes["woo_tuner_remove_breadcrumbs"] = __("Remove Breadcrumbs?","woo-tuner");
							$global_checkboxes["woo_tuner_disable_stylesheets"] = __("Disable WooCommerce stylesheets?","woo-tuner");
							foreach($global_checkboxes as $key=>$value){
								$this->render_checkbox_field($key,$value);
							}
						?>
					</div>
				</div>

				<div class="woo_tuner_section">

					<h3 class="woo_tuner_section_title" data-target="woo-tuner-shop-tax-settings">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
						<?php _e("Shop & Categories Settings","woo-tuner"); ?>
						<button class="button button-secondary button-small select-all-checkboxes">
							<?php _e("Check/Uncheck all","woo-tuner"); ?>
						</button>
					</h3>
					<span class="woo-template-path">
						<span>woocommerce/templates/archive-product.php</span>
						<span>woocommerce/templates/taxonomy-product_cat.php</span>
					</span>

					<div class="woo_tuner_form_fields_wrapper" id="woo-tuner-shop-tax-settings">
						<?php
							$shop_checkboxes = array();
							$shop_checkboxes["woo_tuner_remove_taxonomy_archive_description"] = __("Remove Taxonomy Archive description?","woo-tuner");
							$shop_checkboxes["woo_tuner_remove_product_archive_description"] = __("Remove Product Archive description?","woo-tuner");
							$shop_checkboxes["woo_tuner_remove_taxonomy_result_count"] = __("Remove Result Count?","woo-tuner");
							$shop_checkboxes["woo_tuner_remove_taxonomy_catalog_ordering"] = __("Remove Catalog Ordering?","woo-tuner");
							$shop_checkboxes["woo_tuner_remove_taxonomy_pagination"] = __("Remove Taxonomy Pagination?","woo-tuner");
							foreach($shop_checkboxes as $key=>$value){
								$this->render_checkbox_field($key,$value);
							}
						?>
					</div>
				</div>

				<div class="woo_tuner_section">

					<h3 class="woo_tuner_section_title" data-target="woo-tuner-single-product-settings">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
						<?php _e("Single Product Settings","woo-tuner"); ?>
						<button class="button button-secondary button-small select-all-checkboxes">
							<?php _e("Check/Uncheck all","woo-tuner"); ?>
						</button>
					</h3>
					<span class="woo-template-path">woocommerce/templates/content-single-product.php</span>

					<div class="woo_tuner_form_fields_wrapper" id="woo-tuner-single-product-settings">
						<?php
							$single_product_checkboxes = array();
							$single_product_checkboxes["woo_tuner_remove_product_title"]      = __("Remove Single Product title?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_rating"]      = __("Remove Single Product rating?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_price"]       = __("Remove Single Product price?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_excerpt"]     = __("Remove Single Product excerpt?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_add_to_cart"] = __("Remove Single Product Add to Cart?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_meta"]        = __("Remove Single Product Meta?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_sharing"]     = __("Remove Single Product Sharing?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_sale_flash"]  = __("Remove Single Product Sale Flash?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_images"]      = __("Remove Single Product Images?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_data_tabs"]   = __("Remove Single Product Data Tabs?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_single_upsell"]      = __("Remove Single Product Upsell?","woo-tuner");
							$single_product_checkboxes["woo_tuner_remove_related_products"]   = __("Remove Related Products?","woo-tuner");

							foreach($single_product_checkboxes as $key=>$value){
								$this->render_checkbox_field($key,$value);
							}
						?>

					</div>
				</div>

				<div class="woo_tuner_section">

					<h3 class="woo_tuner_section_title" data-target="woo-tuner-product-content-loops">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
						<?php _e("Product content within loops","woo-tuner"); ?>
						<button class="button button-secondary button-small select-all-checkboxes">
							<?php _e("Check/Uncheck all","woo-tuner"); ?>
						</button>
					</h3>
					<span class="woo-template-path">
						<span>woocommerce/templates/content-product.php</span>
					</span>

					<div class="woo_tuner_form_fields_wrapper" id="woo-tuner-product-content-loops">
						<?php
							$product_content_checkboxes = array();
							$product_content_checkboxes["woo_tuner_remove_product_loop_sale_flash"] = __("Remove Product Loop Sale Flash?","woo-tuner");
							$product_content_checkboxes["woo_tuner_remove_product_loop_thumbnail"] = __("Remove Product Loop Thumbnail?","woo-tuner");
							$product_content_checkboxes["woo_tuner_remove_product_loop_title"] = __("Remove Product Loop Title?","woo-tuner");
							$product_content_checkboxes["woo_tuner_remove_product_loop_rating"] = __("Remove Product Loop Rating?","woo-tuner");
							$product_content_checkboxes["woo_tuner_remove_product_loop_price"] = __("Remove Product Loop Price?","woo-tuner");
							$product_content_checkboxes["woo_tuner_remove_product_loop_add_to_cart"] = __("Remove Product Loop Add to cart button?","woo-tuner");
							foreach($product_content_checkboxes as $key=>$value){
								$this->render_checkbox_field($key,$value);
							}
						?>
					</div>
				</div>

				<?php submit_button(); ?>

			</form>

			<?php else: ?>

				<h3><?php _e("Please, install WooCommerce first","woo-tuner"); ?></h3>
				<p><a href="https://wordpress.org/plugins/woocommerce/" target="_blank"><?php _e("Install WooCommerce","woo-tuner"); ?></a></p>
			<?php endif; ?>

		</div>

		<?php
	}

	public function update_single_product_widgets() {

		$response = array();
		$newOrder = isset($_POST['newOrder']) ? $_POST['newOrder'] : '';
		if($newOrder){
			$newOrder = serialize($newOrder);
			update_option('woocommerce_single_product_summary_widgets_order', $newOrder);
			$response['type'] = 'success';
		} else {
			$response['type'] = 'error';
		}
		echo json_encode($response);
		die();
	}

	public function get_woocommerce_single_product_summary_widgets(){
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

		$single_product_elements = array();

		$single_product_elements['woocommerce_template_single_title'] = array(
			'title'    => 'Single Product Title',
			'active'   => get_option('woo_tuner_remove_product_title') ? 'checked-in': 'not-checked-in',
			'order'    => 1,
			'priority' => 5,
			'option'   => 'woo_tuner_remove_product_title'
		);
		$single_product_elements['woocommerce_template_single_rating'] = array(
			'title'    => 'Single Product Rating',
			'active'   => get_option('woo_tuner_remove_single_rating') ? 'checked-in': 'not-checked-in',
			'order'    => 2,
			'priority' => 10,
			'option'   => 'woo_tuner_remove_single_rating'
		);
		$single_product_elements['woocommerce_template_single_price'] = array(
			'title'    => 'Single Product Price',
			'active'   => get_option('woo_tuner_remove_single_price') ? 'checked-in': 'not-checked-in',
			'order'    => 3,
			'priority' => 10,
			'option'   => 'woo_tuner_remove_single_price'
		);
		$single_product_elements['woocommerce_template_single_excerpt'] = array(
			'title'    => 'Single Product Excerpt',
			'active'   => get_option('woo_tuner_remove_single_excerpt') ? 'checked-in': 'not-checked-in',
			'order'    => 4,
			'priority' => 20,
			'option'   => 'woo_tuner_remove_single_excerpt'
		);
		$single_product_elements['woocommerce_template_single_add_to_cart'] = array(
			'title'    => 'Single Product Add to cart',
			'active'   => get_option('woo_tuner_remove_single_add_to_cart') ? 'checked-in': 'not-checked-in',
			'order'    => 5,
			'priority' => 30,
			'option'   => 'woo_tuner_remove_single_add_to_cart'
		);
		$single_product_elements['woocommerce_template_single_meta'] = array(
			'title'    => 'Single Product Meta',
			'active'   => get_option('woo_tuner_remove_single_meta') ? 'checked-in': 'not-checked-in',
			'order'    => 6,
			'priority' => 40,
			'option'   => 'woo_tuner_remove_single_meta'
		);
		$single_product_elements['woocommerce_template_single_sharing'] = array(
			'title'    => 'Single Product Sharing',
			'active'   => get_option('woo_tuner_remove_single_sharing') ? 'checked-in': 'not-checked-in',
			'order'    => 7,
			'priority' => 50,
			'option'   => 'woo_tuner_remove_single_sharing'
		);

		if( get_option('woocommerce_single_product_summary_widgets_order') ) {
			$new_single_product_elements = array();
			$saved_single_product_elements = get_option('woocommerce_single_product_summary_widgets_order');
			$saved = unserialize($saved_single_product_elements);
			foreach($saved as $key=>$hook){
				$new_single_product_elements[$hook] = array(
					'title'    => $single_product_elements[$hook]['title'],
					'active'   => $single_product_elements[$hook]['active'],
					'order'    => $single_product_elements[$hook]['order'],
					'priority' => $single_product_elements[$hook]['priority'],
					'option'   => $single_product_elements[$hook]['option']
				);
			}
			return $new_single_product_elements;
		}

		return $single_product_elements;
	}

	public function render_woo_tuner_order_widgets(){

		$single_product_elements = $this->get_woocommerce_single_product_summary_widgets();

		?>

		<div class="woo-tuner-admin-wrapper">

			<div class="woo-tuner-loader-wrapper">
				<div class="woo-tuner-loader">Loading...</div>
			</div>

			<span class="woo-tuner-author">
				<a href="http://volkov.co.il" target="_blank">Created by @vol4ikman</a>
			</span>

			<h1><?php _e("Woo Tuner - Widgets Order","woo-tuner"); ?></h1>
			<div class="woo-tuner-description">
				<?php _e("Reorder template components with simple drag & drop functionality.","woo-tuner"); ?>
			</div>

			<div class="drag-block-wrapper">
				<h3>Single Product summary elements order</h3>
				<div class="drag-block-content">
					<ul id="sortable-signle-product" class="sortable-list">
						<?php foreach($single_product_elements as $hook=>$params): ?>
							<li id="<?php echo $hook; ?>" class="ui-state-default <?php echo $params['active']; ?>"
								data-priority="<?php echo $params['priority']; ?>"
								data-option="<?php echo $params['option']; ?>">
								<span class="dashicons dashicons-menu"></span>
								<?php echo $params['title']; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

	<?php }

	public function render_checkbox_field($id,$label){
			$value = get_option($id);
			$checked = false;
			if($value && !empty($value)){
				$checked = true;
			}
		?>
		<div class="woo_tuner_form_row clearfix">
			<input name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="wootuner_checkbox" type="checkbox"
				<?php if($checked): ?>checked<?php endif; ?> data-enable="<?php echo $checked ? 'off' : 'on'; ?>" />
			<label for="<?php echo $id; ?>"><?php echo $label; ?></label>
		</div>
	<?php }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'icheck-green', plugin_dir_url( __FILE__ ) . 'css/green.css', array(), NULL, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-tuner-admin.css', array(), $this->version, 'all' );
		if(is_rtl()){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-tuner-admin-rtl.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'icheck', plugin_dir_url( __FILE__ ) . 'js/icheck.min.js', array( 'jquery' ), NULL, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-tuner-admin.js', array( 'jquery' ), $this->version, false );

	}

}
