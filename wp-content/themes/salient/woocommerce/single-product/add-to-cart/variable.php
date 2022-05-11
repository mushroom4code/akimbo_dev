<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = json_encode( $available_variations );
$variations_attr = json_decode($variations_json);
do_action( 'woocommerce_before_add_to_cart_form' );
 ?>

<form class="variations_form cart"
      action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
      method="post" enctype='multipart/form-data' data-product_id="<?php echo $product->get_id() ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php

    /**
     * @var $product WC_Product
     */
    $attributes = $attribute_keys;
    $children = $variations_attr;

    $color = $attributes["pa_tsvet-tkani"];
    $structure = $attributes["pa_tkan-verha"];
    $podkladka = $attributes["pa_podkladka-2"];
    $rost = $attributes["pa_rost-fotomodeli-2"];
    $default_structure = $attributes['pa_structure'];
    $default_podkladka = $attributes['pa_podkladka'];
    $default_color = $attributes['pa_color'];
    $default_rost = $attributes['pa_rost-fotomodeli'];

    $sorted_attributes = array();

    if (in_array($structure, $attributes)) {
        array_push($sorted_attributes, $structure);
    } elseif (in_array($default_structure, $attributes)) {
        array_push($sorted_attributes, $default_structure);
    }

    if (in_array($podkladka, $attributes)) {
        array_push($sorted_attributes, $podkladka);
    } elseif (in_array($default_podkladka, $attributes)) {
        array_push($sorted_attributes, $default_podkladka);
    }

    if (in_array($color, $attributes)) {
        array_push($sorted_attributes, $color);
    } elseif (in_array($default_color, $attributes)) {
        array_push($sorted_attributes, $default_color);
    }

    if (in_array($rost, $attributes)) {
        array_push($sorted_attributes, $rost);
    } elseif (in_array($default_rost, $attributes)) {
        array_push($sorted_attributes, $default_rost);
    }

    foreach($sorted_attributes as $attribute){

        if(in_array($attribute->get_name(), $attributes)){
            $attr_name = wc_attribute_label( $attribute->get_name());
            $attr_value = $product->get_attribute($attribute->get_name());
            echo "<b>" . $attr_name . ":</b> " . $attr_value . "<br />";
        }
    }

    echo do_shortcode('[wholesale columns="buy" products="'.$product->get_id().'/" buy="horizontal-attribute/razmer"]'); ?>
<!--   <div>-->
<!--        <button type="button" onclick="this.parentNode.querySelector('[type=number]').stepDown();">-->
<!--            --->
<!--        </button>-->
<!---->
<!--        <input type="number" name="number" min="0" max="100" value="0">-->
<!---->
<!--        <button type="button" onclick="this.parentNode.querySelector('[type=number]').stepUp();">-->
<!--            +-->
<!--        </button>-->
<!--    </div>-->
    <?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>
    <style>
        .product-title-row{
            display: none;
        }
    </style>
<?php
do_action( 'woocommerce_after_add_to_cart_form' );
