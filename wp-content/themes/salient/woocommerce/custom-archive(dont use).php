<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
    <header class="woocommerce-products-header">
        <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_archive_description.
         *
         * @hooked woocommerce_taxonomy_archive_description - 10
         * @hooked woocommerce_product_archive_description - 10
         */
        do_action( 'woocommerce_archive_description' );
        ?>
    </header>
<?php
if ( woocommerce_product_loop() ) {

    woocommerce_product_loop_start();
    $parentid = get_queried_object_id();
    $args = array(
        'parent' => $parentid
    );
 
    $current = isset($current) ? $current : wc_get_loop_prop('current_page');
    /**
     * Hook: woocommerce_before_shop_loop.
     *
     * @hooked woocommerce_output_all_notices - 10
     * @hooked woocommerce_result_count - 20
     * @hooked woocommerce_catalog_ordering - 30
     */
    do_action( 'woocommerce_before_shop_loop' );
    if($parentid == 84){
        $total = 999999999999999999 ;
    }else{
        $total = 27;
    }

    $totalProdInPage =  $total;

    $cat = get_term($parentid, '', 'OBJECT ');
    $categId = $cat->term_id;
    $CategoryUrl = get_term_link($categId);
    $child = get_term_children($categId, $cat->taxonomy);

    $ordering_args = WC()->query->get_catalog_ordering_args();
    if($ordering_args ['orderby'] == 'totalsale'){
        $orderby = 'total_sales';
        $order = 'DESC';
    }elseif ($ordering_args ['orderby'] == 'price-desc'){
        $orderby = '_price';
        $order = 'DESC';
    }elseif ($ordering_args ['orderby'] == 'price'){
        $orderby = '_price';
        $order = 'ASC';
    }elseif ($ordering_args ['orderby'] == 'date' ){
        $orderby = '_wc1c_guid';
        $order = 'DESC';
    }

    

    $products = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $totalProdInPage,
        'paged' => $current, // номер текущей страницы
        "orderby" => 'meta_value',
        "meta_key" => $orderby,
        "order" =>$order,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $cat->slug,

            ),
        )
    ));

    foreach($products->posts as $single_product) {
        $preorder = 0;
        $is_upcoming = 0;
        $prod_obj = wc_get_product( $single_product->ID);
        $isset_img = $prod_obj->get_image_id();
        $productPrice = $prod_obj->get_price();
        $productQuantity = $prod_obj->get_stock_quantity();
        $productIsBackOrder = $prod_obj->get_backorders();


        if($parentid == 84) $is_upcoming = 1;

        if($productPrice == 0 && $productQuantity == 0 && $productIsBackOrder == 'yes') $preorder = 1;

        if(empty($isset_img) ||
            ($productQuantity <= 0  && 
            $productIsBackOrder !== 'yes' && 
            !$preorder && 
            !$is_upcoming)
        )continue;
        
        $post_object = get_post($single_product);
        setup_postdata($GLOBALS['post'] =& $post_object);
        wc_get_template_part('content', 'product');

    }

    ?>


    <?php

    woocommerce_product_loop_end();

    /**
     * Hook: woocommerce_after_shop_loop.
     *
     * @hooked woocommerce_pagination - 10
     */
    do_action( 'woocommerce_after_shop_loop' );
} else {
    /**
     * Hook: woocommerce_no_products_found.
     *
     * @hooked wc_no_products_found - 10
     */
    do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
