<?php
	if ( ! defined( 'ABSPATH' ) ) { 
	    exit; // Exit if accessed directly
	}
?>

<table class="wholesale products">
    
    <?php 
        
        while( WS_Table::get_connection() ) {
            
            do_action( 'wholesale_before_content' );
                
            do_action( 'wholesale_content' );
                
            do_action( 'wholesale_afer_content' );
    		
        }
        
    ?>
    
</table>