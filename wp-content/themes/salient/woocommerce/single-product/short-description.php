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
    $children = $product->get_children();

    $color = $attributes["pa_tsvet-tkani"];
    $structure = $attributes["pa_tkan-verha"];
    $podkladka = $attributes["pa_podkladka-2"];
    $rost = $attributes["pa_rost-fotomodeli-2"];
    $default_structure = $attributes['pa_structure'];
    $default_podkladka = $attributes['pa_podkladka'];
    $default_color = $attributes['pa_color'];
    $default_rost = $attributes['pa_rost-fotomodeli'];
    //create array

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

    // end
    foreach($sorted_attributes as $attribute){

        if(in_array($attribute->get_name(), $available_attr)){
            $attr_name = wc_attribute_label( $attribute->get_name());
            $attr_value = $product->get_attribute($attribute->get_name());
            echo "<b>" . $attr_name . ":</b> " . $attr_value . "<br />";
        }
    }

    echo '<p>'.$descriptionProduct[0].'</p>'; // WPCS: XSS ok.
    echo do_shortcode('[wholesale columns="buy,tally/Шт,total/Итого" products="'.$prodId.'/Сделайте заказ по размерам" buy="horizontal-attribute/razmer"]');

    // Если у товара есть все атрибуты длин, отобразить таблицу размеров
    if (isset($attributes['pa_dlina-izdeliya']) || isset($attributes['pa_dlina-po-vnutrennemu-shvu']) || isset($attributes['pa_dlina-po-vneshnemu-shvu'])) {

        foreach ($children as $child) {
            $children_data[] = $product->get_children_data($child);
        }

        $column_name = ['Размер', 'Длина изделия (см)', 'Длина по внутреннему шву (см)', 'Длина по внешнему шву (см)'];
        $size = explode(", ", $product->get_attribute($attributes['pa_razmer']->get_name()));

        foreach ($children_data as $child_data) {
            if (isset($child_data['attribute_pa_dlina-izdeliya']))
                $length[] = $child_data['attribute_pa_dlina-izdeliya'];

            if (isset($child_data['attribute_pa_dlina-po-vneshnemu-shvu']))
                $size_outer[] = $child_data['attribute_pa_dlina-po-vneshnemu-shvu'];

            if (isset($child_data['attribute_pa_dlina-po-vnutrennemu-shvu']))
                $size_inner[] = $child_data['attribute_pa_dlina-po-vnutrennemu-shvu'];
        }

        echo '<div class="length_table"><table class="wholesale products">';

        echo '<tr>';
        echo '<th class="horizontal-attribute">' . $column_name[0] . '</th>';
        for ($i = 0; $i < 5; $i++) {
            echo '<th class="horizontal-attribute">' . $size[$i] . '</th>';
        }
        echo '</tr>';

        if (isset($attributes['pa_dlina-izdeliya'])) {

            echo '<tr>';
            echo '<th class="horizontal-attribute">' . $column_name[1] . '</th>';

            for ($i = 0; $i < 5; $i++) {
                if (strpos($length[$i][0], '-sm') !== false) {
                    echo '<th class="tally">' . str_replace('-', ',', stristr($length[$i][0], '-sm', true)) . '</th>';
                } else {
                    echo '<th class="tally">' . str_replace('-', ',', $length[$i][0]) . '</th>';
                }
            }
            echo '<tr>';
        }

        if (isset($attributes['pa_dlina-po-vnutrennemu-shvu'])) {

            echo '<tr>';
            echo '<th class="horizontal-attribute">' . $column_name[2] . '</th>';

            for ($i = 0; $i < 5; $i++) {
                if (strpos($size_inner[$i][0], '-sm') !== false) {
                    echo '<th class="tally">' . str_replace('-', ',', stristr($size_inner[$i][0], '-sm', true)) . '</th>';
                } else {
                    echo '<th class="tally">' . str_replace('-', ',', $size_inner[$i][0]) . '</th>';
                }
            }
            echo '<tr>';
        }

        if (isset($attributes['pa_dlina-po-vneshnemu-shvu'])) {

            echo '<tr>';
            echo '<th class="horizontal-attribute">' . $column_name[3] . '</th>';

            for ($i = 0; $i < 5; $i++) {
                if (strpos($size_outer[$i][0], '-sm') !== false) {
                    echo '<th class="tally">' . str_replace('-', ',', stristr($size_outer[$i][0], '-sm', true)) . '</th>';
                } else {
                    echo '<th class="tally">' . str_replace('-', ',', $size_outer[$i][0]) . '</th>';
                }
            }
            echo '<tr>';
        }

        echo '</table></div>';
    }

    echo do_shortcode('[button open_new_tab="true" color="see-through" hover_text_color_override="#b0926f" size="medium" url="/size-table/" text="Таблица размеров" color_override="#b0926f" hover_color_override="#fcfaf7"]');
    ?>
</div>
