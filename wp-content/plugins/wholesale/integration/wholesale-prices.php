<?php

  add_filter( 'wholesale_product_info', 'add_wholesale_table_wholesale_prices_support', 10, 2 );

  function add_wholesale_table_wholesale_prices_support( $product_info, $product ) {

    $user = wp_get_current_user();
    if( !in_array('wholesale_customer', $user->roles ) ) {
      return $product_info;
    }

    $id = $product_info['is_variation'] ? $product_info['variation_id'] : $product_info['product_id'];
    $price = get_post_meta( $id, 'wholesale_customer_wholesale_price', true );
    if( $price != 0 ) {
      $product_info['sale-price'] = $price;
    }

    return $product_info;
  }

?>
