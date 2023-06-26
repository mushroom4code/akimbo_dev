<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Period_Of_Validity_Days for variable products
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

trait YFYM_T_Variable_Get_Period_Of_Validity_Days {
	public function get_period_of_validity_days($tag_name = 'period-of-validity-days', $result_xml = '') {
		$product = $this->get_product();
		$offer = $this->get_offer();
		$tag_value = '';

		$period_of_validity_days = yfym_optionGET('yfym_period_of_validity_days', $this->get_feed_id(), 'set_arr');
		if (empty($period_of_validity_days) || $period_of_validity_days === 'off' || $period_of_validity_days === 'disabled') { } else {
			$period_of_validity_days = (int)$period_of_validity_days;
			$tag_value = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($period_of_validity_days));
			if (empty($tag_value)) {	
				$tag_value = $product->get_attribute(wc_attribute_taxonomy_name_by_id($period_of_validity_days));
			}
		}
		$tag_value = apply_filters('y4ym_f_variable_tag_value_period_of_validity_days', $tag_value, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_variable_tag_name_period_of_validity_days', $tag_name, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_period_of_validity_days', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>