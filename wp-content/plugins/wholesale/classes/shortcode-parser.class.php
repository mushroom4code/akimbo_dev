<?php

    /*
        Class used for parsing shortcode
    */

    class WS_Shortcode_parser {

        //Columns not connected with buying
        public $normal_columns;

        //Columns connected with buying
        public $buy_columns;

        //List of all products
        public $products_list;

        // Settings
        public $settings;

        function __construct( $atts ) {

            $a = shortcode_atts( array(
                'columns' => 'image,buy,sale-price,tally,total',
                'buy' => null,
                'products' => null,
                'categories' => null,
                'tags' => null,
                'button_for_every_table' => 'no',
                'grand_total' => 'no',
                'link_in_title' => 'no',
                'one_product_one_image' => 'no'
            ), $atts );

            $this->normal_columns = $this->parse_columns( $a['columns'] );
            $this->buy_columns = $this->parse_buy_columns( $a['buy'] );
            $this->products_list = $this->parse_product_lists( $a['products'], $a['categories'], $a['tags'] );
            $this->settings = $this->parse_settings( $a );

        }

        /**
         * Parse settings
         *
         * @since 1.1.9
         * @return array
        */
        public function parse_settings( $shortcode ) {

          $settings = array(
                        'button_for_every_table' => null,
                        'grand_total' => null,
                        'link_in_title' => null,
                        'one_product_one_image' => null
                      );

          foreach( $settings as $setting_name => $value ) {
            $settings[ $setting_name ] = $shortcode[ $setting_name ]  === 'no' ? false : true;
          }

          return $settings;

        }

        /**
         * Parse columns
         *
         * @since 1.0.0
         * @return array
        */
        public function parse_columns( $columns_text ) {

            // / - column title at the end

            if( !$columns_text ) {
                return null;
            }

            $columns = array();
            $columns_text = explode( ',', $columns_text );
            $one_column = array();

            foreach( $columns_text as $text ) {

                $temp = explode( '/', $text );

                $one_column['name'] = $temp[0];

                if( !WS_Shortcode_parser::is_allowed_column_type( $one_column['name']) ) {
                    return false;
                }

                $one_column['heading'] = ( count($temp) > 1 ) ? $temp[1] : $temp[0];

                array_push( $columns, $one_column );

            }

            return $columns;
        }

        /**
         * Parse product lists
         *
         * @since 1.0.0
         * @return array
        */
        public function parse_product_lists( $ids, $categories, $tags ) {

            // | - connects products
            // / - product title (at the end)

            if( !$ids && !$categories && !$tags ) {
                if( is_product() ) {
                  //It is single product page with no products specified
		    	        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                  return WS_Shortcode_parser::get_current_product_as_connecton();
                } else {
                  return WS_Shortcode_parser::get_all_products_as_connections();
                }
            }

            return array_merge( $this->parse_ids($ids),
                                $this->parse_taxonomies($categories, $tags)
                              );

        }

        private function parse_ids( $ids ) {

            if( !$ids ) return array();

            $product_lists = array();
            $product_lists_texts = explode( ',', $ids );
            $one_product_list = array();

            foreach( $product_lists_texts as $text ) {
                $temp = explode( '/', $text );
                $one_product_list['IDs'] = explode( '|', $temp[0] );

                if( count($temp) > 1 ) {
                    $one_product_list['title'] = $temp[1];
                } else {
                    $product = get_post( $one_product_list['IDs'][0] );
                    $one_product_list['title']  = $product->post_title;
                }

                array_push( $product_lists, $one_product_list );

            }

            return $product_lists;
        }

        private function parse_taxonomies( $categories, $tags ) {

          $categories = explode( ',', $categories );
          $tags = explode( ',', $tags );

          $args = array(
                        'posts_per_page'   => -1,
                        'offset'           => 0,
                        'orderby'          => 'date',
                        'order'            => 'DESC',
                        'post_type'        => 'product',
                        'post_status'      => 'publish',
                        'tax_query' => array(
                                              'relation' => 'OR',
                                          		array(
                                          			'taxonomy' => 'product_cat',
                                          			'field' => 'slug',
                                          			'terms' => $categories
                                          		),
                                              array(
                                          			'taxonomy' => 'product_tag',
                                          			'field' => 'slug',
                                          			'terms' => $tags
                                          		)
                                          	)
                      );
          $product_list = array();

          $products = get_posts( $args );
          foreach ( $products as $product ) {
              $product_list[] = array('IDs' => array( $product->ID) , 'title' => $product->post_title);
          }


          return $product_list;

        }

        /**
         * Generate list fangle product
         *
         * @since 1.1.3
        */
        public function get_current_product_as_connecton() {
          global $post;
          $id = $post->ID;
          $title = get_the_title( $id );
          return array( array(
            'IDs' => array( $id ),
            'title' => $title
          ));
        }

        /**
         * Generate all products list
         *
         * @since 1.0.0
        */
        public function get_all_products_as_connections() {

            $product_list = array();

            $args = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'orderby'          => 'date',
                'order'            => 'DESC',
                'post_type'        => 'product',
                'post_status'      => 'publish'
            );

            $products = get_posts( $args );

            foreach ( $products as $product ) {
                $product_list[] = array('IDs' => array( $product->ID) , 'title' => $product->post_title);
            }

            return $product_list;
        }

        /**
         * Parse but columns
         *
         * @since 1.0.0
         * @return array
        */
        public function parse_buy_columns( $quantity_text ) {

            if( !$quantity_text ) {
                return null;
            }

            $buy_columns_texts = explode( ',', $quantity_text );
            $buy_columns = array();

            foreach( $buy_columns_texts as $column ) {
                $temp = explode( '/', $column );

                $buy_columns[ $temp[0] ][ 'name' ] = explode( '|', $temp[1] );
            }

            return $buy_columns;
        }

        /**
         * Check if this column is allowed type
         *
         * @since 1.0.0
         * @return bool
        */
        public function is_allowed_column_type( $type ) {
            return in_array( $type, array_keys( Wholesale::$column_types) );
        }

    }

?>
