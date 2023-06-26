<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Weight for variable products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		YFYM_Get_Paired_Tag
*				methods: 	get_product
*							get_offer
*							get_feed_id
*				functions:	
*/

trait YFYM_T_Variable_Get_Weight {
	public function get_weight($tag_name = 'weight', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;
		$tag_value = '';

		$weight_yml = $offer->get_weight(); // вес
		if (!empty($weight_yml)) {
			$tag_value = round(wc_get_weight($weight_yml, 'kg'), 3);
		}

		$tag_value = apply_filters('y4ym_f_variable_tag_value_weight', $tag_value, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_variable_tag_name_weight', $tag_name, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_weight', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>