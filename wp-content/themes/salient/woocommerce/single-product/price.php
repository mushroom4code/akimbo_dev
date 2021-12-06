<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;
// Enterego(V.Mikheev) et display info about out of stock or get back soon
$infoMessage = '';
$handle = new WC_Product_Variable($product->get_id());
$variations1 = $handle->get_children();
$i = 0;
$emptyStock = array();
<<<<<<< HEAD
=======

>>>>>>> ba257fe270aac097438e4a5c128f2c702cbdc430
foreach ($variations1 as $value) {
    $single_variation = new WC_Product_Variation($value);
    if ($single_variation->stock_status == 'outofstock') {
        $emptyStock [] = 1;
    }
    $i++;
}
if (count($emptyStock) == $i) {
    $infoMessage = 'Нет в наличии';
}

$first_date = get_post_meta($product->get_id(),'first_date',true);
$planned_date = get_post_meta($product->get_id(),'planned_date',true);

if ($first_date !== '' && isset($first_date)) {
    echo '';
} else if ($planned_date !== '' && isset($planned_date) && $planned_date !== 'false'){
    echo '<div class="new_data"><b style="">Плановая дата поступления</b>
            <span style="font-weight: 500;font-size: 20px;color: #af8a6e;">' . $planned_date . '</span></div>';
            $infoMessage = '';
}else {
    echo '';
}
<<<<<<< HEAD

=======
>>>>>>> ba257fe270aac097438e4a5c128f2c702cbdc430
echo salePrice($product);
if ($product->get_price() == 0 && $product->get_stock_quantity() == 0 && $product->get_backorders() == 'yes') {
    ?>
    <span class="CustomEmptyPrice">В производстве</span>
<?php } else { ?>
    <p class="<?php echo esc_attr(apply_filters('woocommerce_product_price_class', 'price')); ?>"><?php echo $product->get_price_html() . ' ' . $infoMessage; ?></p>
<?php } ?>


