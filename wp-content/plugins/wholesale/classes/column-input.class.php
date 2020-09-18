<?php

    /*
        Class used for displaying inputs
    */
    
    class WS_Column_input extends WS_Column {
        
        public $key_part;
        
        public function __construct( $name, $key_part = 0, $heading = null ) {
            $this->name = $name;
            $this->heading = $heading;
            $this->key_part = $key_part;
         }

         /**
          * Return cell content
          * 
          * @since 1.0.0
          * @return string
         */
         public function get_cell_content() {
            echo apply_filters('wholesale_input', $this->key_part);
         }
         
    } 
    
?>