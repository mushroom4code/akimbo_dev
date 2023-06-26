<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Store for simple products
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

trait YFYM_T_Simple_Get_Store {
	public function get_store($tag_name = 'store', $result_xml = '') {
		$product = $this->product;

		// Возможность купить товар в розничном магазине. // true или false
		$store = yfym_optionGET('yfym_store', $this->get_feed_id(), 'set_arr');
		if ($store === false || $store == '') {	} else {
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $store);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_store', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>