<?php

    /*
        Class used for displaying columns connected with buying (attributes and inputs)
    */

    class WS_Column_buy extends WS_Column {

      //Array of columns used for buing
      public $quantity_columns = array();

      public function __construct( $name, $quantity_columns = array() ) {
        $this->name = $name;

        $this->create_quantity_columns( $quantity_columns );
        WS_Column::$quantity_column = $this;
      }

      // Returns key
      public function get_key_row() {
        $key = '';
        foreach( $this->quantity_columns as $column ) {
          if( is_a( $column, 'WS_Column_values') ) {
            $key .= sanitize_title($column->get_cell_content()) . '|';
          }
        }

        return $key;
      }

      public function print_heading() {
        foreach( $this->quantity_columns as $column ) {
          echo $column->print_heading();
        }
      }

      public function create_quantity_columns( $columns_array ) {

          //If there aren't any columns specified
          if( $columns_array === null) {
            $products_list = WS_Table::$self->products_list;
            $connection_index = WS_Table::$self->connection_index;
            $ID = $products_list[$connection_index]['IDs'][0];

            $product = new WC_Product_Variable( $ID );

            //Product is a variable
            if( $product->has_child() ) {
              $keys = array_keys( $product->get_variation_attributes() );
              for( $i = 0; $i<count($keys); $i++) {
                $keys[$i] = str_replace( 'pa_', '', $keys[$i] );
              }
              $columns_array = array( 'vertical-attribute' => array( 'name' => $keys ) );
            } else {
              $this->quantity_columns = array( new WS_Column_input( 'horizontal-attribute' ) );
              WS_Table::set_total_rows( 1 );
              return;
            }
          }

          $temp_columns = array();

          $keys = array_keys( $columns_array );

          $height = 1;


          for( $i = count($keys)-1; $i >= 0; $i-- ) {

            switch ( $keys[$i] ) {
              case 'vertical-attribute' :

                foreach( array_reverse( $columns_array[ $keys[$i] ]['name'] ) as $name ) {
                  $values = WS_Column_buy::get_attribute_values( WS_Table::get_connection(), sanitize_title( $name ) );

                  if( taxonomy_is_product_attribute( 'pa_' . sanitize_title( $name ) ) ) {
                    $name = wc_attribute_label( 'pa_' . sanitize_title( $name ) );
                  }

                  array_push( $temp_columns, new WS_Column_values( 'vertical-attribute', $values, $name, $height) );

                  $values_count = count( $values );
                  $height *= $values_count;
                }

              break;
              case 'label' :

                $values = $columns_array[ $keys[$i] ]['name'];

                //We need to limit label values to amount of products in connection
                $connection = WS_Table::get_connection();
                $connection_IDs = $connection['IDs'];
                $values = array_slice( $values, 0, count($connection_IDs) );

                array_push( $temp_columns, new WS_Column_values( 'label', $values, null, $height) );
                $values_count = count( $values );
                $height *= $values_count;

              break;

            }

          }

          WS_Table::set_total_rows( $height );

          $temp_columns = array_reverse( $temp_columns );

          if( isset( $columns_array['horizontal-attribute'] ) ) {
            $values = WS_Column_buy::get_attribute_values( WS_Table::get_connection(), sanitize_title( $columns_array['horizontal-attribute']['name'][0] ) );
            foreach( $values as $value) {
              array_push( $temp_columns, new WS_Column_input( 'horizontal-attribute', sanitize_title( $value ), $value ) );
            }
          } else {
            array_push( $temp_columns, new WS_Column_input( 'horizontal-attribute' ) );
          }

          $this->quantity_columns = $temp_columns;
      }

      /**
       * Return cell content
       *
       * @since 1.0.0
      */
      public function get_cell_content() {
      }

      public function print_cell() {
        foreach( $this->quantity_columns as $column) {
          echo $column->print_cell();
        }
      }

      public static function get_attribute_values( $connection, $name ) {

        $attributes_array = array();

        foreach( $connection['IDs'] as $id ) {

          $product = new WC_Product( $id );
          $attributes = $product->get_attribute($name);

          $explode_by = strstr( $attributes, ' | ' ) ? ' | ' : ', ';
          $attributes = explode( $explode_by, $attributes );

            foreach( $attributes as $attribute ) {
              if( !in_array( $attribute, $attributes_array ) ) {
                array_push( $attributes_array, $attribute );
              }
            }

        }

        return $attributes_array;
      }
    }

?>
