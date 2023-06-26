<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Weight for simple products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		YFYM_Get_Paired_Tag
*				methods: 	get_product
*							get_feed_id
*				functions:	
*/

trait YFYM_T_Simple_Get_Weight {
	public function get_weight($tag_name = 'weight', $result_xml = '') {
		$product = $this->product;
		$tag_value = '';

		$weight_yml = $product->get_weight(); // вес
		if (!empty($weight_yml)) {
			$tag_value = round(wc_get_weight($weight_yml, 'kg'), 3);
		}

		$tag_value = apply_filters('y4ym_f_simple_tag_value_weight', $tag_value, array('product' => $product), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_simple_tag_name_weight', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_weight', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>