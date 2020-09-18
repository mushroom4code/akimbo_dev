<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /*
        Parent class of all columns
    */

    abstract class WS_Column {

        //Object of WC_Column_buying
        public static $quantity_column = null;

        //Current row index
        public static $row_index = 0;

        //Column heading, can be empty
        public $heading = null;

        //Column name. Used as a cell class name
        public $name;

        //Row height, used to create tall cells
        public $row_height = 1;

        /**
         * Print cell
         *
         * @since 1.0.0
        */
        public function print_cell() {
          $row = WS_Products_content::get_row_index();

          // Change for photos
          if( $this->name == 'image' && WS_Table::$self->settings['one_product_one_image'] == true ) {
            $this->row_height = WS_Table::$self->total_rows;
          }
          if( $row % $this->row_height === 0) {
            echo '<td rowspan="' . $this->row_height . '" class="' . $this->name . '">';
            echo $this->get_cell_content();
            echo '</td>';
          }
          if( $row % $this->row_height === $this->row_height-1 ) {
            $this->move();
          }
        }

        /**
         * Print heading
         *
         * @since 1.0.0
        */
        public function print_heading() {
          echo '<th class="' . $this->name . '" >';
          echo $this->heading;
          echo '</th>';
        }

        /**
         * Return cell content
         *
         * @since 1.0.0
         * @return string
        */
        abstract public function get_cell_content();

        /**
         * Used for changing content in columns, not all columns need that, so it does not have to be abstract
         *
         * @since 1.0.0
        */
        public function move() {
          //Should be empty
        }

        /**
         * Return column heading
         *
         * @since 1.0.0
         * @return string
        */
        public function get_heading() {
          return $heading;
        }

    }

?>
