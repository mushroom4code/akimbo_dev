<?php

    /*
        Class used for empty cells, which content will be created by js script (tally,total)
    */
    
    class WS_Column_empty extends WS_Column {
    
        public function __construct( $name, $heading = null, $row_height = 1) {
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
          return '0';
        }
    
    }

?>
