<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits CategoryId for simple products
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
*							get_catid
*				functions:	
*/

trait YFYM_T_Simple_Get_CategoryId {
	public function get_categoryid($tag_name = 'categoryId', $result_xml = '') {
		$product = $this->get_product();
		$tag_value = '';
		
		$tag_value = $this->get_feed_category_id();

		$tag_value = apply_filters('y4ym_f_simple_tag_value_categoryid', $tag_value, array('product' => $product), $this->get_feed_id());
		if (!empty($tag_value)) {
			$tag_name = apply_filters('y4ym_f_simple_tag_name_categoryid', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		// для совместимости со старыми версиями PRO
		$result_xml = apply_filters('yfym_after_cat_filter', $result_xml, $product->get_id(), $this->get_feed_id());

		$result_xml = apply_filters('y4ym_f_simple_tag_categoryid', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>