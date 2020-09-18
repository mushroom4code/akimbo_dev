<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    class WS_Products_content {

       private static $self = null;

       //Current row key (like 'black|big|metal|')
       public $row_key = 0;

       //Current row index
       public $row_index = 0;

       //Products info array
       public $products = array();

       //One product from current row, if there is not one it is null
       public $row_product;
       
       public $product_id;

       public static function init( $connection, $quantity_columns = null ) {

            if( !WS_Products_content::$self ) {
                WS_Products_content::add_hooks();
            }

            return WS_Products_content::$self = new WS_Products_content( $connection, $quantity_columns );

        }

        private function __construct( $connection, $quantity_columns ) {
            $this->generate_product_list( $connection, $quantity_columns );
        }

        private static function add_hooks() {
            add_filter( 'wholesale_row_value', array( 'WS_Products_content', 'get_row_value' ) );
            add_filter( 'wholesale_input', array( 'WS_Products_content', 'get_input' ) );
        }

        /**
         * Prints content rows
         *
         * @since 1.0.0
        */
        public function print_rows() {

            for( $i = 0; $i < Wholesale::$WS_Table->total_rows; $i++) {

                $key = WS_Column::$quantity_column->get_key_row();

                if( !Wholesale::$WS_Table->quantity_columns && !$key ) {
                    $key = $this->row_index;
                }

                $this->set_key( $key );

                $this->print_row();

                $this->row_index++;
            }

        }

        /**
         * Prints single row
         *
         * @since 1.0.0
        */
        public function print_row() {
            $price = '';
            $product_id = '';

            if( $this->row_product ) {
                $price = 'price="' . $this->row_product['sale-price'] . '" ';
                $product_id = 'product_id="' . $this->product_id . '" ';
            }

            echo '<tr ' . $price . $product_id . '>';
            foreach( Wholesale::$WS_Table->columns as $column ) {
                $column->print_cell();
            }
            echo '</tr>';
        }

        /**
         * Return row index
         *
         * @since 1.0.0
         * @return integer
        */
        public static function get_row_index() {
            return WS_Products_content::$self->row_index;
        }

        /**
         * Return input, used with action wholesale_inpue
         *
         * @since 1.0.0
         * @return string
        */
        public static function get_input( $key ) {
            $input;
    
            if( isset( WS_Products_content::$self->products[WS_Products_content::$self->row_key][$key] ) ) {
                $input = new Input_creator( WS_Products_content::$self->products[WS_Products_content::$self->row_key][$key] );
            } else {
                $input = new Input_creator( null );
            }
            return $input->get_input();
        }

        /**
         * Generate products list
         *
         * @since 1.0.0
        */
        private function generate_product_list( $connection, $quantity_columns ) {

            $products = array();
            $i = 0;

            foreach( $connection['IDs'] as $ID ) {
                $this->product_id = $ID;

                $product = new WC_Product_Variable( $ID );
                //var_dump($product);
                //Product is a variable
                if( $product->has_child() ) {

                    //If there aren't any specifed attributes
                    if( ! ( isset( $quantity_columns['vertical-attribute'] ) || isset( $quantity_columns['horizontal-attribute'] ) ) ) {
                      $keys = array_keys( $product->get_variation_attributes() );
                      for( $i = 0; $i<count($keys); $i++) {
                        $keys[$i] = str_replace( 'pa_', '', $keys[$i] );
                      }
                      $quantity_columns = array( 'vertical-attribute' => array( 'name' => $keys ) );
                    }

                    //Get all variations
                    $variations = $product->get_available_variations();

                    foreach( $variations as $variation_info ) {

                        $variation = new WC_Product_Variation( $variation_info['variation_id'] );

                        $product_info = WS_Products_content::get_product_info( $variation, true );
                        
                        if( !$product_info ) {
                            break;
                        }

                        array_push( $this->products, $product_info );

                        $key = '';

                        //Generate array key
                        foreach( $quantity_columns as $name => $column ) {

                            switch( $name ) {

                                case 'vertical-attribute':

                                    foreach( $column['name'] as $attribute ) {
                                        $key .= sanitize_title( $product_info['attributes'][sanitize_title($attribute)] ) . '|';
                                    }

                                break;

                                case 'label':

                                    if( isset( $column['name'][$i] ) ) {
                                        $key .= sanitize_title( $column['name'][$i] ) . '|';
                                    }

                                break;

                                case 'horizontal-attribute':

                                    foreach( $column['name'] as $attribute ) {
                                        $products[$key][sanitize_title( $product_info['attributes'][sanitize_title($attribute)] )] = $product_info;
                                    }

                                break;

                            }

                        }

                        //There are no horizontal attributes
                        if( !isset( $quantity_columns['horizontal-attribute'] ) ) {
                            $products[$key] = array( $product_info );
                        }

                    }

                    $this->products = $products;

                } else {

                    //Simple products
                    $product_info = WS_Products_content::get_product_info( new WC_Product($ID) );
                    //var_dump($product_info);
                    if( isset( $quantity_columns['label']['name'][$i] ) ) {
                        $this->products[ sanitize_title($quantity_columns['label']['name'][$i] ) . '|' ] = array( $product_info );
                    } else {
                        array_push( $this->products, array( $product_info ) );
                    }

                }

                $i++;
            }
        }

        /**
         * Return value of row product, used with wholesale_row_value action
         *
         * @since 1.0.0
         * @return string|int|bool
        */
        public static function get_row_value( $name ) {
            if( WS_Products_content::$self->row_product ) {
                return WS_Products_content::$self->row_product[$name];
            } else {
                return null;
            }
        }

        /**
         * Set key, and change row_product
         *
         * @since 1.0.0
        */
        public function set_key( $key ) {
            $this->row_key = $key;
            $this->row_product = $this->get_product_from_row( $key );
        }

        /**
         * Get product from row specified by key
         *
         * @since 1.0.0
         * @return array|null
        */
        public function get_product_from_row( $key ) {

            if( !isset( $this->products[$key] ) ) {
                return null;
            }

            foreach( $this->products[$key] as $product_info ) {
                return $product_info;
            }

        }

        /**
         * Get product info
         *
         * @since 1.0.0
         * @return array|null
        */
        public static function get_product_info( $product, $is_variation = false ) {
            
            if( !$product->is_purchasable() ) {
                return null;
            }

            $options = array(
                'sell_as_one' => $product->is_sold_individually(), //!== false  ? true : false,
                'manage_stock' => $product->managing_stock(),
                'allow_backorders' => $product->backorders_allowed()
            );
            
            $product_info = array(
                'options' => $options,
                'is_variation' => $is_variation,
                'stock' => $product->get_stock_quantity(),
                'is_on_sale' => $product->is_on_sale(),
                'is_in_stock' => $product->is_in_stock(),
                'sale-price' => $product->get_sale_price() ? $product->get_sale_price() : wc_get_price_to_display($product),
                'regular-price' => $product->get_regular_price(),
                'product_id' => $product->get_id(),
                'SKU' => $product->get_sku(),
                'image' => $product->get_image(),
                'rating' => wc_get_rating_html($product->get_average_rating()),
                'title' => $product->get_title(),
            );
            
            if( $is_variation ) {
                $all_attributes_set = true;
                
                // undefined attributes have null strings as array values
                foreach ( $product->get_variation_attributes() as $att ) {
                	if ( ! $att ) {
                		$all_attributes_set = false;
                		break;
                	} 
                }
                if( !$all_attributes_set ) {
                    //var_dump($all_attributes_set);
                    //return null;
                }

                $product_info['variation_id'] = $product->get_id();
                $product_info['attributes'] = array();
                $attributes = $product->get_variation_attributes();

                foreach( $attributes as $key => $value) {

                    $key = str_replace( 'attribute_', '', $key );

                    if( strpos( $key, 'pa_' ) !== false ) {
                        $slugs = wc_get_product_terms( $product->get_id(), $key, array( 'fields' => 'slugs' ) );
                        $slugs = array_merge($slugs);
                        $names =  wc_get_product_terms( $product->get_id(), $key, array( 'fields' => 'names' ) );


                        $key = str_replace( 'pa_', '', $key );
                        $array_search_key = array_search($value, $slugs);
                        if($array_search_key) $value = $names[$array_search_key];
                    }

                    $product_info['attributes'][$key] = $value;

                }


                //Get product image
                $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large' );
                if( $large == '' ) {
                  $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large' );
                }
                
            } else {

                //Get product image
                $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large' );

            }

            $small = $product->get_image( 'shop_thumbnail' );
            $product_info['image'] = '<a href="' . $large[0] . '" rel="lightbox">' . $small . '</a>';

            
            //Filters for customization
            $product_info = apply_filters( 'wholesale_product_info', $product_info, $product );
            //($product_info);
            return $product_info;
        }

    }

?>
