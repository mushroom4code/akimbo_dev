<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Country_Of_Orgin for variable products
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
*				functions:	yfym_optionGET 
*/

trait YFYM_T_Variable_Get_Country_Of_Orgin {
	public function get_country_of_origin($tag_name = 'country_of_origin', $result_xml = '') {
		$product = $this->get_product();
		$offer = $this->get_offer();
		$tag_value = '';

		$country_of_origin = yfym_optionGET('yfym_country_of_origin', $this->get_feed_id(), 'set_arr');
		if (empty($country_of_origin) || $country_of_origin === 'off' || $country_of_origin === 'disabled') { } else {
			$country_of_origin = (int)$country_of_origin;
			$tag_value = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($country_of_origin));
			if (empty($tag_value)) {	
				$tag_value = $product->get_attribute(wc_attribute_taxonomy_name_by_id($country_of_origin));
			}
		}
		$tag_value = apply_filters('y4ym_f_variable_tag_value_country_of_origin', $tag_value, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_variable_tag_name_country_of_origin', $tag_name, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_country_of_origin', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>