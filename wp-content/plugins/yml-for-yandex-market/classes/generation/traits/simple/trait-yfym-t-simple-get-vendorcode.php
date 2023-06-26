<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Vendorcode for simple products
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
*							add_skip_reason
*				functions:	yfym_optionGET 
*/

trait YFYM_T_Simple_Get_Vendorcode {
	public function get_vendorcode($tag_name = 'vendorCode', $result_xml = '') {
		$product = $this->get_product();
		$tag_value = '';
		 
		$yfym_vendorcode = yfym_optionGET('yfym_vendorcode', $this->get_feed_id(), 'set_arr');
		switch ($yfym_vendorcode) {
			case "disabled": // выгружать штрихкод нет нужды		
				break; 
			case "sku": // выгружать из артикула
				$tag_value = $product->get_sku();
				break;
			default:
				$tag_value = apply_filters('y4ym_f_simple_tag_value_switch_barcode', $tag_value, array('product' => $product, 'switch_value' => $yfym_vendorcode), $this->get_feed_id());			
				if ($tag_value == '') {
					$yfym_vendorcode = (int)$yfym_vendorcode;
					$tag_value = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_vendorcode));			
				}
		}

		$tag_value = apply_filters('y4ym_f_simple_tag_value_vendorcode', $tag_value, array('product' => $product), $this->get_feed_id());
		if (!empty($tag_value)) {
			$tag_name = apply_filters('y4ym_f_simple_tag_name_vendorcode', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_vendorcode', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>