<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Url for simple products
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
*				functions:	yfym_optionGET
*							yfym_replace_domain
*							get_from_url
*/

trait YFYM_T_Simple_Get_Url {
	public function get_url($tag_name = 'url', $result_xml = '') {
		$product = $this->product;
		$tag_value = '';

		$tag_value = htmlspecialchars(get_permalink($product->get_id())); // урл товара
		$yfym_clear_get = yfym_optionGET('yfym_clear_get', $this->get_feed_id(), 'set_arr');
		if ($yfym_clear_get === 'yes') {$tag_value = get_from_url($tag_value, 'url');} // удаляем get-параметры
		$tag_value = apply_filters('yfym_url_filter', $tag_value, $product, $this->get_feed_category_id(), $this->get_feed_id());
		$tag_value = apply_filters('yfym_simple_url_filter', $tag_value, $product, $this->get_feed_category_id(), $this->get_feed_id());	

		$tag_value = apply_filters('y4ym_f_simple_tag_value_url', $tag_value, array('product' => $product), $this->get_feed_id());
		if (!empty($tag_value)) {	
			$tag_name = apply_filters('y4ym_f_simple_tag_name_url', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = yfym_replace_domain($result_xml, $this->get_feed_id());
		$result_xml = apply_filters('y4ym_f_simple_tag_url', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>