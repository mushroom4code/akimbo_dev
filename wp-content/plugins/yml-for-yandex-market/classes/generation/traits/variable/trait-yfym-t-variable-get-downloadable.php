<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Downloadable for variable products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:	YFYM_Get_Paired_Tag
*				methods: add_skip_reason
*				functions: 
*/

trait YFYM_T_Variable_Get_Downloadable {
	public function get_downloadable($tag_name = 'downloadable', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;
		$tag_value = '';

		$downloadable = yfym_optionGET('yfym_downloadable', $this->get_feed_id(), 'set_arr');
		if (!empty($downloadable) && $downloadable !== 'off') {
			if ($offer->is_downloadable('yes')) {
				$tag_value = 'true';
			} else {
				$tag_value = 'false';							
			}
		}

		$tag_value = apply_filters('y4ym_f_variable_tag_value_downloadable', $tag_value, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_variable_tag_name_downloadable', $tag_name, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_downloadable', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>