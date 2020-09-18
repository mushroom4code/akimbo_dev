<?php
/*
Plugin Name: Woocommerce Wholesale
Description: WooCommerce Wholesale is a WooCommerce e-store extension that allows to create custom product tables, which make buying far more quicker and convenient.
Text Domain: wholesale
Version: 1.2.4
Author: OptArt
Author URI: http://www.optart.biz
*/

if( !class_exists( 'Wholesale' ) )  {

    class Wholesale {

        //WS_Table object used for displaying products in table
        public static $WS_Table;

        //Available colum types
        public static $column_types = array(
            'SKU' => 'simple',
            'excerpt' => 'simple',
            'buy' => 'buy',
            'regular-price' => 'simple',
            'sale-price' => 'simple',
            'rating' => 'simple',
            'image' => 'simple',
            'stock' => 'simple',
            'title' => 'simple',
            'tally' => 'empty',
            'total' => 'empty'
        );

        /**
         * Init plugin
         *
         * @since 1.0.0
        */
        public static function init() {
            Wholesale::add_shortcodes();
            Wholesale::add_hooks();

            require_once( Wholesale::get_path() . 'classes/wholesale_ajax.class.php');
        }

        /**
         * Add hooks
         *
         * @since 1.0.0
        */
        private static function add_hooks() {
            add_action( 'wp_enqueue_scripts', array( 'Wholesale', 'add_styles_and_scripts' ) );
            add_action( 'plugins_loaded', array( 'Wholesale', 'load_textdomain' ) );
        }

        /**
         * Add style and script
         *
         * @since 1.0.0
        */
        public static function add_styles_and_scripts() {

            //Basic plugin styles
            wp_enqueue_style( 'wholesale', Wholesale::get_url() . 'assets/css/wholesale.css' );

            //Script used for buying products
            wp_register_script( 'wholesale', Wholesale::get_url() . 'assets/js/wholesale.js', array('jquery') );

        }

        /**
         * Add shortcode
         *
         * @since 1.0.0
        */
        public static function add_shortcodes() {
            add_shortcode( 'wholesale', array( 'Wholesale', 'get_table' ) );
        }

        /**
    	 * Return products table in a string. Used in shortcode for displaying table.
    	 *
    	 * @since 1.0.0
    	 * @return string
	    */
        public static function get_table( $shortcode ) {

            //Just in case somebody wanted to add custom column
            Wholesale::$column_types = apply_filters( 'wholesale_column_types', Wholesale::$column_types );

            Wholesale::require_files();

            $parser = new WS_Shortcode_parser( $shortcode );
            $settings = $parser->settings;
            $settings['ajax_url'] = admin_url( 'admin-ajax.php' );

            wp_localize_script( 'wholesale', 'wholesale_settings', $settings );

            ob_start();

            Wholesale::$WS_Table = WS_Table::init( $parser->products_list, $parser->normal_columns, $parser->buy_columns, $settings );

            Wholesale::$WS_Table->print_table();

            wp_enqueue_script( 'wholesale' );

            include( Wholesale::get_path() . 'templates/add-to-cart-button.php');

            $buffer = ob_get_contents();
            ob_end_clean();
            return $buffer;
        }

        /**
    	 * Load plugin textdomain.
    	 *
    	 * @since 1.0.0
	    */
        public static function load_textdomain() {
            load_plugin_textdomain( 'wholesale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
        }

        /**
    	 * Add needed files
    	 *
    	 * @since 1.0.0
	    */
        public static function require_files() {
            if( class_exists('Incremental_Product_Quantities') ) {
               require_once( Wholesale::get_path() . 'integration/incremental-product-quantities.php' );
            }
            if( class_exists('WWP_Wholesale_Prices') ) {
              require_once( Wholesale::get_path() . 'integration/wholesale-prices.php' );
            }
            require_once( Wholesale::get_path() . 'classes/shortcode-parser.class.php');
            require_once( Wholesale::get_path() . 'classes/column.class.php');
            require_once( Wholesale::get_path() . 'classes/column-simple.class.php');
            require_once( Wholesale::get_path() . 'classes/column-empty.class.php');
            require_once( Wholesale::get_path() . 'classes/column-buy.class.php');
            require_once( Wholesale::get_path() . 'classes/column-input.class.php');
            require_once( Wholesale::get_path() . 'classes/column-vertical-names.class.php');
            require_once( Wholesale::get_path() . 'classes/products-table.class.php');
            require_once( Wholesale::get_path() . 'classes/products-content.class.php');
            require_once( Wholesale::get_path() . 'classes/input-creator.class.php');
        }

        /*
         * Returns string with plugin URL
         *
         * @return string
        */
        public static function get_url() {
            return plugin_dir_url( __FILE__ );
        }

        /*
         * Returns string with plugin URL
         *
         * @return string
        */
        public static function get_path() {
            return plugin_dir_path( __FILE__ );
        }


    }

}

if( class_exists( 'Wholesale') ) {
    Wholesale::init();
}

?>
