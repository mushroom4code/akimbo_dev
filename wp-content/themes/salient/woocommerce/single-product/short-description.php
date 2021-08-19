<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $post;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
// Enterego(V. Mikheev) delete select variations size and button add to cart from product description

//if ( ! $short_description ) {
//    return;
//}

$descriptionProduct = explode('[',$post->post_excerpt );
$prodId = $post->ID;

?>

<div class="woocommerce-product-details__short-description">
    <?php
    /**
     * Enterego | Zhukov
     * Вывод необходимой информации о атрибутах товара
     */

    global $product;

    $available_attr = ['pa_color', 'pa_structure', 'pa_size', 'pa_rost-fotomodeli', 'pa_podkladka', 'pa_tsvet-tkani', 'pa_tkan-verha', 'pa_podkladka-2', 'pa_rost-fotomodeli-2'];

    /**
     * @var $product WC_Product
     */
    $attributes = $product->get_attributes();

    $color = $attributes["pa_tsvet-tkani"];
    $structure = $attributes["pa_tkan-verha"];
    $podkladka = $attributes["pa_podkladka-2"];
    $rost = $attributes["pa_rost-fotomodeli-2"];

    if (in_array($color, $attributes)) {
        unset($attributes["pa_color"]);
    }

    if (in_array($structure, $attributes)) {
        unset($attributes["pa_structure"]);
    }

    if (in_array($podkladka, $attributes)) {
        unset($attributes["pa_podkladka"]);
    }

    if (in_array($rost, $attributes)) {
        unset($attributes["pa_rost-fotomodeli"]);
    }

    foreach($attributes as $attribute){

        if(in_array($attribute->get_name(), $available_attr)){
            $attr_name = wc_attribute_label( $attribute->get_name());
            $attr_value = $product->get_attribute($attribute->get_name());
            echo "<b>" . $attr_name . ":</b> " . $attr_value . "<br />";
        }
    }

    echo '<p>'.$descriptionProduct[0].'</p>'; // WPCS: XSS ok.
    echo do_shortcode('[wholesale columns="buy,tally/Шт,total/Итого" products="'.$prodId.'/Сделайте заказ по размерам" buy="horizontal-attribute/razmer"]');
    echo do_shortcode('[button open_new_tab="true" color="see-through" hover_text_color_override="#b0926f" size="medium" url="/size-table/" text="Таблица размеров" color_override="#b0926f" hover_color_override="#fcfaf7"]');
    ?>
</div>
