<?php

if( ! function_exists( 'salient_quick_shop' ) ) {
    function salient_quick_shop($id = false) {
        if( isset($_GET['id']) ) {
            $id = (int) $_GET['id'];
        }

        global $post;

        $args = array( 'post__in' => array($id), 'post_type' => 'product' );

        $quick_posts = get_posts( $args );

        foreach( $quick_posts as $post ) :
            setup_postdata($post);
            woocommerce_template_single_add_to_cart();
        endforeach;

        wp_reset_postdata();

        die();
    }

    add_action( 'wp_ajax_quick_shop', 'salient_quick_shop' );
//    add_action( 'wp_ajax_nopriv_quick_shop', 'salient_quick_shop' );
}