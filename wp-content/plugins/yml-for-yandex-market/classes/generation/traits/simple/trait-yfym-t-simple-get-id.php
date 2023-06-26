<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits for simple products
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

trait YFYM_T_Simple_Get_Id {
	public function get_id() {
		$product = $this->product;
		$result_xml_id = $product->get_id();
		$yfym_instead_of_id = yfym_optionGET('yfym_instead_of_id', $this->get_feed_id(), 'set_arr');
		if ($yfym_instead_of_id === 'sku') {
			$sku_xml = $product->get_sku();
			if (!empty($sku_xml)) {$result_xml_id = htmlspecialchars($sku_xml);}
		}
		// $result_xml_id = apply_filters('yfym_simple_result_xml_id', $result_xml_id, $yfym_instead_of_id, $this->get_input_data_arr());

		$result_xml_id = apply_filters('yfym_simple_result_xml_id', $result_xml_id, $product, $yfym_instead_of_id, $this->get_feed_id()); /* c версии 2.3.12 */

		$result_xml_id = new YFYM_Get_Paired_Tag('id', $result_xml_id);
		return $result_xml_id; 
	}
}
?>