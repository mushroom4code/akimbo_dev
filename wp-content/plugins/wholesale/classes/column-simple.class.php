<?php

    /*
        Class used for displaying product info
    */
    
    class WS_Column_simple extends WS_Column {
    
      public function __construct( $name, $heading = null, $row_height = 1 ) {
        $this->name = $name;
        $this->heading = $heading;
        $this->row_height = $row_height;
      }
      
      /**
       * Return cell content
       * 
       * @since 1.0.0
       * @return string
      */
      public function get_cell_content() {
        echo apply_filters( 'wholesale_row_value', $this->name );
      }
    }

?>
