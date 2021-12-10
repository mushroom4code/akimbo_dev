<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

$wcCart = WC()->cart;
$wcCartCollection = $wcCart->get_cart_contents();

if(!empty($_POST['action']) && $_POST['action'] == 'remove_product_basket') {

    $deleted_items = array();
    $product_id = $_POST['product_id'];

    if(!empty($wcCartCollection) && is_array($wcCartCollection)) {
        if($product_id != 'full') {
            foreach($wcCartCollection as $cart_item_key => $cart_item) {
                if($cart_item['product_id'] == $product_id) {
                    if($wcCart->remove_cart_item($cart_item_key)) {
                        $deleted_items[] = $product_id;
                    }
                }
            }

        } elseif($product_id == 'full') {
            $wcCart->empty_cart();
            $items = $_POST['items'];
            if(!empty($items) && is_array($items)) {
                foreach($items as $item) {
                    $quantity = apply_filters('woocommerce_stock_amount', $item['quantity']);
                    if(isset($item['variation_id']) && isset($item['variation'])) {
                        $wcCart->add_to_cart($item['product_id'], $quantity, $item['variation_id'], $item['variation']);
                    } else {
                        $wcCart->add_to_cart($item['product_id'], $quantity);
                    }
                }
            }
        }
        $wcCartCollection = $wcCart->get_cart_contents();
    }
}

do_action('woocommerce_before_cart');

unset($_SESSION['gryaka']);
foreach($wcCartCollection as $cart_item_key => $cart_item) {
    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

    if(isset($_SESSION['gryaka']) && isset($_SESSION['gryaka'][$product_id])) {
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['max'] = $_product->get_max_purchase_quantity();
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['key'] = $cart_item['key'];
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['quantity'] = $cart_item['quantity'];
        $_SESSION['gryaka'][$product_id]['skip'] = $_SESSION['gryaka'][$product_id]['skip'] + 1;
        continue;
    } else {
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['max'] = $_product->get_max_purchase_quantity();
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['key'] = $cart_item['key'];
        $_SESSION['gryaka'][$product_id][$cart_item['variation_id']]['quantity'] = $cart_item['quantity'];
        $_SESSION['gryaka'][$product_id]['skip'] = 0;
    }

}
$arProdId = $arProdId_mob = $_SESSION['gryaka'];
?>
<form class="woocommerce-cart-form ee_mobile_form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>
    <div class="clear_basket_box">
        <button type="submit" class="button clear_basket" name="clear-cart"
                onclick='javascript:if(!confirm("Удалить все товары из корзины?")) {return false;}'> Очистить Корзину
        </button>
    </div>
    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
        <tr>
            <th class="product-remove">&nbsp;</th>
            <th class="product-thumbnail">&nbsp;</th>
            <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
            <th class="product-quantity"
                style="text-align: center"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            <th class="product-subtotal"><?php esc_html_e('Total', 'woocommerce'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        do_action('woocommerce_before_cart_contents');
        foreach($wcCartCollection as $cart_item_key => $cart_item) {


            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);


            $args = array(
                'post_type' => 'product_variation',
                'post_status' => array('private', 'publish'),
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_parent' => $product_id // get parent post-ID
            );
            $variations = get_posts($args);


            if($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key) && $arProdId[$product_id]['skip'] == 0) {
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                    <td class="product-remove" style="cursor:pointer">
                        <a class="remove" data-product_id="<?= $product_id ?>">&times;</a>
                    </td>
                    <td class="product-thumbnail ee_mobile">
                        <?php
                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                        if(!$product_permalink) {
                            echo $thumbnail; // PHPCS: XSS ok.
                        } else {
                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                        }


                        //name
                        ?>
                        <div class="product_name_link">
                            <?
                            if(!$product_permalink) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                            } else {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_title()), $cart_item, $cart_item_key));
                            }
                            // Meta data.
                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.
                            //                        // Backorder notification.
                            if($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                            }
                            ?>
                        </div>
 

                        <!--   price   -->

                        <div class="product_price">
                            <?
                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                            ?>
                        </div>

                    </td>

                    <td class="product-quantity ee_mobile">
                        <?php
                        $prod_offer = new WC_Product_Variable($product_id);
                        $prod_offer = $prod_offer->get_variation_attributes();
                        $variation_size = array();

                        if(isset($prod_offer['pa_razmer'])) {
                            $variation_size = $prod_offer['pa_razmer'];
                        }
						sort($variation_size);
                        ?>
                        <div class="wholebasket wholesale products">
                            <div class="headings">
                                <?
                                foreach($variation_size as $size): ?>
                                    <div class="wholehole headwhole horizontal-attribute"><span
                                                class="prop_offer"><?= $size ?></span></div>
                                <? endforeach; ?>
                                <div class="wholehole headwhole horizontal-attribute backgr_count_item"><span
                                            class="prop_offer">Шт</span></div>
                            </div>
                            <div class="bodyings">
                                <?
								$temp_ar = $sOffers = [];
                                foreach($variations as $variation){
                                    $o_variation = wc_get_product($variation->ID);
                                    $temp_ar[$o_variation->get_attribute('pa_razmer')] = $variation->ID;
                                }
                                ksort($temp_ar);
								foreach($temp_ar as $temp_v) {
									foreach($variations as $variation) {
                                        if($temp_v == $variation->ID){
                                            $sOffers[] = $variation;
                                        }
                                    }
                                }
                                unset($temp_ar);
                                $variations = $sOffers;
                                $row_quant = $count_all = 0;

                                foreach($variations as $variation) {
                                    $o_variation = wc_get_product($variation->ID);
                                    if(!$o_variation->get_attribute('pa_razmer')) continue;

                                    $disabled = !$o_variation->get_price() ? ' disabled' : '';
                                    if(isset($arProdId_mob[$product_id][$variation->ID])) {
                                        $quantity = $arProdId_mob[$product_id][$variation->ID]['quantity'];
                                        $row_quant = $row_quant + $quantity;
                                    } else {
                                        $quantity = empty($disabled) ? 0 : '';
                                    }
                                    $max = empty($disabled) ? $o_variation->get_stock_quantity() : 0;
                                    if($max == 0){
                                        $quantity = '';
                                        $disabled = ' disabled';
                                    }
                                    ?>
                                    <div class="wholehole bodywhole horizontal-attribute">
                                        <input <?=$disabled?> type="number" class="product-quantity" min="0"
                                               name="<?= $variation->ID ?>" value="<?= $quantity ?>"
                                               max="<?= $max ?>" product_id="<?= $product_id ?>"
                                               attr_razmer="<?= $o_variation->get_attribute('pa_razmer') ?>">
                                    </div>

                                    <?
                                    $count_all = $quantity + $count_all;
                                }
                                ?>
                                <div class="wholehole bodywhole horizontal-attribute backgr_count_item">
                                    <span><?= $count_all ?></span></div>

                            </div>
                        </div>
                    </td>

                    <td class="product-subtotal" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
                        <?php
                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $row_quant), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                        ?>
                    </td>
                </tr>
                <?php
            } else {
                $arProdId[$product_id]['skip'] = $arProdId[$product_id]['skip'] - 1;
            }
        }
        ?>

        <?php do_action('woocommerce_cart_contents'); ?>

        <tr>
            <td colspan="6" class="actions">

                <?php if(wc_coupons_enabled()) { ?>
                    <div class="coupon">
                        <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input
                                type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                                placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"/>
                        <button type="submit" class="button" name="apply_coupon"
                                value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                <?php } ?>

                <button type="submit" class="button" name="update_cart" disabled
                        value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>


                <?php do_action('woocommerce_cart_actions'); ?>

                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </td>
        </tr>

        <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>
    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<form class="woocommerce-cart-form ee_desc_form " action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>
    <div class="clear_basket_box">
        <button type="submit" class="button clear_basket" name="clear-cart"
                onclick='javascript:if(!confirm("Удалить все товары из корзины?")) {return false;}'> Очистить Корзину
        </button>
    </div>
    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
        <tr>
            <th class="product-remove">&nbsp;</th>
            <th class="product-thumbnail">&nbsp;</th>
            <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
            <th class="product-quantity"
                style="text-align: center"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            <th class="product-subtotal"><?php esc_html_e('Total', 'woocommerce'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        do_action('woocommerce_before_cart_contents');

        foreach($wcCartCollection as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
            $args = array(
                'post_type' => 'product_variation',
                'post_status' => array('private', 'publish'),
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_parent' => $product_id // get parent post-ID
            );
            $variations = get_posts($args);


            if($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key) && $arProdId_mob[$product_id]['skip'] == 0) {
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                    <td class="product-remove" style="cursor:pointer">
                        <a class="remove" data-product_id="<?= $product_id ?>">&times;</a>
                    </td>
                    <td class="product-thumbnail ee_desc">
                        <?php
                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                        if(!$product_permalink) {
                            echo $thumbnail; // PHPCS: XSS ok.
                        } else {
                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                        }
                        ?>
                    </td>

                    <td class="product-name ee_desc" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                        <?php
                        if(!$product_permalink) {
                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                        } else {
                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_title()), $cart_item, $cart_item_key));
                        }

                        // Meta data.
                        echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                        //                        // Backorder notification.
                        if($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                            echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                        }
                        ?> 
                    </td>

                    <td class="product-price ee_desc" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                        <?php
                       // Enterego(V.Mikheev) for add to cart product with empty price and stock
                       if($_product->get_stock_quantity() == 0 && $_product->get_backorders()== 'yes' && $_product->get_price() == 0 ){
                        echo ' <span  style="font-size: 15px!important;" class="CustomEmptyPrice">В производстве</span>';
                       }else{
                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                        }
                        ?>
                    </td>


                    <td class="product-quantity ee_desc" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                        <?php
                        $prod_offer = new WC_Product_Variable($product_id);
                        $prod_offer = $prod_offer->get_variation_attributes();
                        $variation_size = array();

                        if(isset($prod_offer['pa_razmer'])) {
                            $variation_size = $prod_offer['pa_razmer'];
                        }
						sort($variation_size);
                        ?>


                        <table class="wholebasket wholesale products">
                            <tbody attributes="razmer ">
                            <tr class="headings" style="background: #cecece0d">
                                <?
                                foreach($variation_size as $size): ?>
                                    <td class="wholehole headwhole horizontal-attribute"> <?= $size ?></td>
                                <? endforeach; ?>
                                <td class="wholehole bodywhole horizontal-attribute backgr_count_item"><span>Шт</span>
                                </td>
                            </tr>
                            <tr class="bodyings">

                                <?
								$temp_ar = $sOffers = [];
                                foreach($variations as $variation){
                                    $o_variation = wc_get_product($variation->ID);
                                    $temp_ar[$o_variation->get_attribute('pa_razmer')] = $variation->ID;
                                }
                                ksort($temp_ar);
								foreach($temp_ar as $temp_v) {
									foreach($variations as $variation) {
                                        if($temp_v == $variation->ID){
                                            $sOffers[] = $variation;
                                        }
                                    }
                                }
                                unset($temp_ar);
                                $variations = $sOffers;
                                $row_quant = $count_all = 0;
                                foreach($variations as $variation) {
                                    $o_variation = wc_get_product($variation->ID);
                                    if(!$o_variation->get_attribute('pa_razmer')) continue;

                                    $disabled = '';
                                    if(isset($arProdId_mob[$product_id][$variation->ID])) {
                                        $quantity = $arProdId_mob[$product_id][$variation->ID]['quantity'];
                                        $row_quant = $row_quant + $quantity;
                                    } else {
                                        $quantity = empty($disabled) ? 0 : '';
                                    }
                                    if($o_variation->get_price() == 0){
                                        $max = 99;
                                    }else{
                                        $max = $o_variation->get_stock_quantity();
                                    }

                                    ?>
                                    <td class="wholehole bodywhole horizontal-attribute">
                                        <input <?=$disabled?> type="number" class="product-quantity" min="0"
                                               name="<?= $variation->ID ?>" value="<?= $quantity ?>"
                                               max="<?= $max ?>" product_id="<?= $product_id ?>"
                                               attr_razmer="<?= $o_variation->get_attribute('pa_razmer') ?>">
                                    </td>

                                    <?
                                    $count_all = $quantity + $count_all;
                                }
                                ?>
                                <td class="wholehole bodywhole horizontal-attribute backgr_count_item"><?= $count_all ?></td>
                            </tr>
                            </tbody>
                        </table>


                    </td>

                    <td class="product-subtotal" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
                        <?php
                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $row_quant), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                        ?>
                    </td>
                </tr>
                <?php
            } else {
                $arProdId_mob[$product_id]['skip'] = $arProdId_mob[$product_id]['skip'] - 1;
            }
        }
        ?>

        <?php do_action('woocommerce_cart_contents'); ?>

        <tr>
            <td colspan="6" class="actions">

                <?php if(wc_coupons_enabled()) { ?>
                    <div class="coupon">
                        <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input
                                type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                                placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"/>
                        <button type="submit" class="button" name="apply_coupon"
                                value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                <?php } ?>

                <button type="submit" class="button" name="update_cart" disabled
                        value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>


                <?php do_action('woocommerce_cart_actions'); ?>

                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </td>
        </tr>

        <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>
    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>
