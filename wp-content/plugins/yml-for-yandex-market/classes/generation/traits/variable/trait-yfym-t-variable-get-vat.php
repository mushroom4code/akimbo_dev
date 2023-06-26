<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Vat for variable products
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

trait YFYM_T_Variable_Get_Vat {
	public function get_vat($tag_name = 'vat', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;

		$vat = yfym_optionGET('yfym_vat', $this->get_feed_id(), 'set_arr');
		if ($vat === 'disabled') {$result_yml_vat = '';} else {
			if (get_post_meta($product->get_id(), 'yfym_individual_vat', true) !== '') {$individual_vat = get_post_meta($product->get_id(), 'yfym_individual_vat', true);} else {$individual_vat = 'global';}
			if ($individual_vat === 'global') {
				if ($vat === 'enable') {
					$result_yml_vat = '';
				} else {
					$result_yml_vat = "<vat>".$vat."</vat>".PHP_EOL;
				}
			} else {
				$result_yml_vat = "<vat>".$individual_vat."</vat>".PHP_EOL;
			}
		}
		$result_xml = $result_yml_vat;

		$result_xml = apply_filters('y4ym_f_variable_tag_vat', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>