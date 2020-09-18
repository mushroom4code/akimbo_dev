<?php


    class WS_Table {

        public static $self = null;

        //Content to print
        private $content = null;

        //Array of WC_Column children objects
        public $columns;

        //Products list as connections
        public $products_list;

        //Index of current connection
        public $connection_index = 0;

        //Total amount of rows
        public $total_rows = 1;

        //Current row index
        public $row_index = 0;

        //Array of columns connected with buying
        public $buy_columns;

        //Array of columns
        public $normal_columns;

        //Array of settings
        public $settings;

        public static function init( $products_list, $normal_columns, $buy_columns, $settings ) {

            if( !WS_Table::$self ) {
                WS_Table::add_hooks();
            }

            WS_Table::$self = new WS_Table( $products_list, $normal_columns, $buy_columns, $settings );
            return WS_Table::$self;
        }

        private function __construct( $products_list, $normal_columns, $buy_columns, $settings ) {
            $this->products_list = $products_list;
            $this->quantity_columns = $buy_columns;
            $this->normal_columns = $normal_columns;
            $this->settings = $settings;
        }

        /**
         * Add hooks
         *
         * @since 1.0.0
        */
        private static function add_hooks() {

            add_action('wholesale_before_content', array( 'WS_Table', 'generate_columns'), 1);
            add_action('wholesale_before_content', array( 'WS_Table', 'open_tbody'), 5);
            add_action('wholesale_before_content', array( 'WS_Table', 'print_title'), 10);
            add_action('wholesale_before_content', array( 'WS_Table', 'print_headings'), 11);

            add_action('wholesale_content', array( 'WS_Table', 'print_content'), 10);

            add_action('wholesale_afer_content', array( 'WS_Table', 'close_tbody'), 80);
            add_action('wholesale_afer_content', array( 'WS_Table', 'move'), 100);
        }

        /**
         * Set amount of rows
         *
         * @since 1.0.0
        */
        public static function set_total_rows( $amount ) {
            WS_Table::$self->total_rows = $amount;
        }

        /**
         * Get row index
         *
         * @since 1.0.0
         * @return int
        */
        public static function get_row_index() {
            return WS_Table::$self->row_index;
        }

        /**
         * Print table
         *
         * @since 1.0.0
        */
        public static function print_table() {
            WS_Table::get_template( 'products-table' );
        }

        /**
         * Add tbody opening html tag with information about attributes. Used with action wholesale_before_content
         *
         * @since 1.0.0
        */
        public static function open_tbody() {
            echo '<tbody ';

            $attributes = '';
            if( isset( WS_Table::$self->quantity_columns['horizontal-attribute'] ) ) {
                $attributes .= sanitize_title( WS_Table::$self->quantity_columns['horizontal-attribute']['name'][0] ) . ' ';
            }
            if( isset( WS_Table::$self->quantity_columns['vertical-attribute'] ) ) {
                foreach( WS_Table::$self->quantity_columns['vertical-attribute']['name'] as $attribute ) {
                     $attributes .= sanitize_title($attribute) . ' ';
                }
            }

            if( WS_Table::$self->quantity_columns === null) {
              $products_list = WS_Table::$self->products_list;
              $connection_index = WS_Table::$self->connection_index;
              $ID = $products_list[$connection_index]['IDs'][0];

              $product = new WC_Product_Variable( $ID );

              //Product is a variable
              if( $product->has_child() ) {
                $keys = array_keys( $product->get_variation_attributes() );
                for( $i = 0; $i<count($keys); $i++) {
                  $attributes .= str_replace( 'pa_', '', $keys[$i] ) . ' ';
                }
              }
              }

            if( $attributes !== '' ) {
                echo 'attributes="' . $attributes . '" ';
            }

            echo '>';
        }

        /**
         * Close tbody tag. Used with action wholesale_after_content
         *
         * @since 1.0.0
        */
        public static function close_tbody() {
            echo '</tobdy>';
        }

        /**
         * Print content
         *
         * @since 1.0.0
        */
        public static function print_content() {

            WS_Table::$self->content = WS_Products_content::init( WS_Table::get_connection(), WS_Table::$self->quantity_columns );

            WS_Table::$self->content->print_rows();

        }

        /**
         * Print headings
         *
         * @since 1.0.0
        */
        public static function print_headings() {

            echo '<tr class="headings">';

            foreach( WS_Table::$self->columns as $column ) {
                $column->print_heading();
            }

            echo '</tr>';

        }

        /**
         * Print title
         *
         * @since 1.0.0
        */
        public static function print_title() {

            $columns_count = count(  WS_Table::$self->columns ) + count( WS_Column::$quantity_column->quantity_columns ) - 1;
            $connection = WS_Table::get_connection();

            echo '<tr class="product-title-row"><th class="product-title" colspan="' . $columns_count . '" >';

            //Version with and without the link
            if( WS_Table::$self->settings['link_in_title'] ) {
              // Get a link to first item
              $link = get_permalink( $connection['IDs'][0] );
              echo '<a href="' . $link . '" class="wholesale-product-link" target="_blank">';
              echo $connection['title'];
              echo '</a>';
            } else {
              echo $connection['title'];
            }

            echo '</th></tr>';
        }

        /**
         * Set columns (renamed)
         *
         * @since 1.0.0
        */
        public static function generate_columns() {

            $normal_columns = WS_Table::$self->normal_columns;
            $buy_columns = WS_Table::$self->quantity_columns;

            $columns = array();

            foreach( $normal_columns as $column ) {

                switch ( Wholesale::$column_types[ $column['name'] ] ) {

                    case 'simple':      array_push( $columns, new WS_Column_simple( $column['name'], $column['heading'] ) );
                                        break;

                    case 'empty':       array_push( $columns, new WS_Column_empty( $column['name'], $column['heading'] ) );
                                        break;

                    case 'buy':         array_push( $columns, new WS_Column_buy( $column['name'], $buy_columns) );
                                        break;

                }
            }

            WS_Table::$self->columns = $columns;
        }

        /**
         * Get current connection
         *
         * @since 1.0.0
         * @return array|null
        */
        public static function get_connection() {
            $index = WS_Table::$self->connection_index;

            if( $index >= count(WS_Table::$self->products_list) ) {
                return null;
            }

            return WS_Table::$self->products_list[$index];
        }

        /**
         * Increment connection index
         *
         * @since 1.0.0
        */
        public static function move() {
            WS_Table::$self->connection_index++;
        }

        /**
         * Helper function used for including templates
         *
         * @since 1.0.0
        */
        public static function get_template( $name ) {
            include( Wholesale::get_path() . 'templates/' . $name . '.php' );
        }
    }

?>
