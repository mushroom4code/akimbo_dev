<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    class Wholesale_AJAX {

        public static function add_hooks() {
            add_action( 'wp_ajax_nopriv_wholesale_add_to_cart', array('Wholesale_AJAX', 'add_products_to_cart') );
            add_action( 'wp_ajax_wholesale_add_to_cart', array('Wholesale_AJAX', 'add_products_to_cart') );
        }

        /**
         * Add products (variations too) to cart
         *
         * @since 1.0.0
        */
        public static function add_products_to_cart() {

            ob_start();

            if( !isset($_POST) ) {
                wp_die();
            }

            if( !isset($_POST['items']) || !count($_POST['items']) ) {
                wp_die();
            }

            $woocommerce_cart = WC()->cart;

            foreach( $_POST['items'] as $item ) {

                $quantity = apply_filters( 'woocommerce_stock_amount', $item['quantity'] );

                if( $quantity < 1 ) {
                    continue;
                }

                if( isset( $item['variation_id'] ) && isset( $item['variation'] ) ) {
                    $woocommerce_cart->add_to_cart($item['product_id'], $quantity, $item['variation_id'], $item['variation']  );
                } else {
                    $woocommerce_cart->add_to_cart( $item['product_id'], $quantity );
                }

                do_action( 'woocommerce_ajax_added_to_cart', $item['product_id'] );
                wc_add_to_cart_message( $item['product_id'] );

            }

			      WC_AJAX::get_refreshed_fragments();

            wp_die();
        }
    }

    Wholesale_AJAX::add_hooks();

?>
