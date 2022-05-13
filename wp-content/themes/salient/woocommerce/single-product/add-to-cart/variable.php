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
<div><p class="font-14 mt-3 text-center p-0 lh-18">Быстрое добавление в корзину</p><div class="close_window"></div></div>

<form class="variations_form cart"
      action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
      method="post" enctype='multipart/form-data' data-product_id="<?php echo $product->get_id() ?>">
	<?php do_action( 'woocommerce_before_variations_form' );
    echo do_shortcode('[wholesale columns="buy" products="'.$product->get_id().'/" buy="horizontal-attribute/razmer"]');
	?>
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
