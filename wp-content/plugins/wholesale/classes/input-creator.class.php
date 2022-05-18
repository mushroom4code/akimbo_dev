<?php
// TODO - плагин нельзя переопределить, при обновлении надо не потерять доработки
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
    Class used to generate input
*/

class Input_creator
{

    //Product info
    private $product_info;

    public function __construct($product_info)
    {

        $this->product_info = $product_info;

    }

    /**
     * Return input HTML
     *
     * @return string
     * @since 1.0.0
     */
    public function get_input()
    {

        if (!$this->product_info['is_in_stock']) {
            return Input_creator::get_disabled_input();
        }

        $max = $this->get_max();
        $variation_id = $this->get_variation_id_custom();

        $max_int = preg_replace('/[^0-9]/', '', $max);
        $value = $max_int > 0 ? 1 : '';
        $quantity = 0;
//        TODO доработка вывода кол-ва товара, имеющегося в корзине
        foreach (WC()->cart->cart_contents as $item) {
            if($item['variation_id'] === $this->product_info['variation_id']) {
                $quantity = $item['quantity'];
            }
        }

        $attributes = $this->get_attributes();
        $custom_attributes = apply_filters('wholesale_input_custom_attributes', '', $this->product_info);
        $input = '<input type="number" class="product-quantity" min="0" value="'.$quantity.'"' . $max . $variation_id . $attributes . $custom_attributes . '>';
        return $input;
    }

    /**
     * Return disabled input HTML
     *
     * @return string
     * @since 1.0.0
     */
    public static function get_disabled_input()
    {
        return '<input class="product-quantity" type="number" disabled>';
    }

    /**
     * Return max quantity of product as HTML attribute
     *
     * @return string
     * @since 1.0.0
     */
    private function get_max()
    {

        $max = '';

        if ($this->product_info['options']['sell_as_one']) {
            $max = 'max="1" ';
        } elseif ($this->product_info['options']['allow_backorders']) {
        } elseif (isset($this->product_info['max'])) {
            $max = 'max="';
            // We set this to be a big number, because if it is not set then $this->product_info['max']  < $stock will always be true
            $stock = 100000000000;
            if ($this->product_info['options']['manage_stock']) {
                $stock = $this->product_info['stock'];
            }
            $max .= $this->product_info['max'] < $stock ?
                $this->product_info['max'] : $stock;
            $max .= '"';
        } elseif ($this->product_info['options']['manage_stock']) {
            $max = 'max="' . $this->product_info['stock'] . '" ';
        }

        return $max;
    }

    /**
     * Return variation id as HTML attribute
     *
     * @return string
     * @since 1.0.0
     */
    private function get_variation_id_custom()
    {

        $variation_id = '';

        //If it is a variation
        if (isset($this->product_info['variation_id'])) {
            $variation_id = 'variation_id="' . $this->product_info['variation_id'] . '"';
        }

        return $variation_id;
    }

    /**
     * Return product attributes list as HTML attributes
     *
     * @return string
     * @since 1.0.0
     */
    private function get_attributes()
    {

        $attributes = '';

        if (isset($this->product_info['attributes'])) {

            foreach ($this->product_info['attributes'] as $name => $value) {
                $attributes .= 'attr_' . sanitize_title($name) . '="' . $value . '" ';
            }

        }

        return $attributes;
    }

}

?>
