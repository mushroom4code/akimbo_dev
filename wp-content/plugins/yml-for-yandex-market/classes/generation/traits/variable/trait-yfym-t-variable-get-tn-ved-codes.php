<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Tn_Ved_Codes for variable products
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
*							add_skip_reason
*				functions:	yfym_optionGET 
*/

trait YFYM_T_Variable_Get_Tn_Ved_Codes {
	public function get_tn_ved_codes($tag_name = 'tn-ved-codes', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;

		if (get_post_meta($product->get_id(), '_yfym_tn_ved_code', true) !== '') {
			$tag_value = get_post_meta($product->get_id(), '_yfym_tn_ved_code', true);
			$result_xml .= new YFYM_Get_Open_Tag($tag_name);
			$result_xml .= new YFYM_Get_Paired_Tag($tag_name, $tag_value);
			$result_xml .= new YFYM_Get_Closed_Tag($tag_name);
		}	

		$result_xml = apply_filters('y4ym_f_variable_tag_tn_ved_code', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>