<?php

    /*
        This colum is used fo vertical attributes and lables
    */
    
    class WS_Column_values extends WS_Column {
        
        //Array of values to display
        private $values;
        
        //Current index
        private $index = 0;
        
        public function __construct( $name, $values, $heading = null, $row_height = 1 ) {
            $this->name = $name;
            $this->heading = $heading;
            $this->row_height = $row_height;
            $this->values = $values;
         }
         
        /**
         * Return cell content
         * 
         * @since 1.0.0
         * @return string
        */ 
         public function get_cell_content() {
            return $this->values[ $this->index ];
         }
         
         /**
          * Change value
          * 
          * @since 1.0.0
         */
         public function move() {
             $this->index++;
             if( $this->index == count($this->values) ) {
                 $this->index = 0;
             }
         }
         
    } 
    
?>