<?php

  function wholesale_add_max_min_step_to_info( $product_info, $product ) {
    $rule =  wpbo_get_applied_rule( $product );
    $new_options = wpbo_get_value_from_rule( 'all', $product, $rule );
    if( isset( $new_options['step'] ) ) {
      $product_info['step'] = $new_options['step'];
    }
    if( isset( $new_options['min_value'] ) ) {
      $product_info['min'] = $new_options['min_value'];
    }
    if( isset( $new_options['max_value'] ) ) {
        $product_info['max'] = $new_options['max_value'];
    }
    return $product_info;
  }

  add_filter( 'wholesale_product_info', 'wholesale_add_max_min_step_to_info', 10, 2 );

  function wholesale_add_custom_attributes_to_info( $custom_attributes, $product_info ) {
    $attributes = '';
    if( isset( $product_info['step'] ) ) {
      $attributes .= ' step="'. $product_info['step'] .'"';
    }
    if( isset( $product_info['min'] ) ) {
      $attributes .= 'ws_min="'. $product_info['min'] .'"';
    }
    return $custom_attributes . $attributes;
  }

  add_filter( 'wholesale_input_custom_attributes', 'wholesale_add_custom_attributes_to_info', 10, 2 );

 ?>
