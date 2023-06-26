<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Model for variable products
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

trait YFYM_T_Variable_Get_Model {
	public function get_model($tag_name = 'model', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;

		$tag_value = '';
		 
		$yfym_model = yfym_optionGET('yfym_model', $this->get_feed_id(), 'set_arr');
		switch ($yfym_model) {
			case "disabled": // выгружать штрихкод нет нужды		
				break; 
			case "sku": // выгружать из артикула
				$tag_value = $offer->get_sku();
				if (empty($tag_value)) { 
					$tag_value = $product->get_sku();
				}
				break;
			default:
				$tag_value = apply_filters('y4ym_f_variable_tag_value_switch_model', $tag_value, array('product' => $product, 'offer' => $offer, 'switch_value' => $yfym_model), $this->get_feed_id());			
				if ($tag_value == '') {
					$yfym_model = (int)$yfym_model;
					$tag_value = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_model));
					if (empty($tag_value)) {	
						$tag_value = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_model));
					}			
				}
		}

		$tag_value = apply_filters('y4ym_f_variable_tag_value_model', $tag_value, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		if (!empty($tag_value)) {
			$tag_name = apply_filters('y4ym_f_variable_tag_name_model', $tag_name, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_model', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;	
	}
}
?>