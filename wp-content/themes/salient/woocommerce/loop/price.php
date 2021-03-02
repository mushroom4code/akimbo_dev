<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;
// Enterego(V.Mikheev) et display info about out of stock or get back soon
$infoMessage= '';
$handle = new WC_Product_Variable($product->get_id());
$variations1 = $handle->get_children();
$i=0;
$emptyStock = array();
foreach ($variations1 as $value) {
    $single_variation = new WC_Product_Variation($value);
    if ($single_variation->stock_status == 'onbackorder' && $product->get_stock_quantity() == 0) {
        $infoMessage = 'Скоро в наличии';
    }elseif ($single_variation->stock_status == 'outofstock'){
        $emptyStock [] = 1;
    }
    $i++;
}
if( count($emptyStock) == $i){
    $infoMessage = 'Нет в наличии';
}



// Enterego(V.Mikheev) for add to cart product with empty price and stocks
if ($product->get_price() == 0 && $product->get_stock_quantity() == 0 && $product->get_backorders() == 'yes') {
    ?>
    <span style="margin-top: 0px; font-size: 15px" class="CustomEmptyPrice">В производстве</span>
<?php } else {
    if ($price_html = $product->get_price_html()) : ?>
        <span class="price"><?php echo $price_html .' '.$infoMessage ?></span>
    <?php endif;
}

