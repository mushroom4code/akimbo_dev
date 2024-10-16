<?php if (!defined('ABSPATH')) {exit;}
/**
 * Traits Offer_Tag for simple products
 *
 * @package				YML for Yandex Market
 * @subpackage		
 * @since				3.9.0
 * 
 * @version				1.0.0 (23-12-2022)
 * @author				Maxim Glazunov
 * @link				https://icopydoc.ru/
 * @see				
 *
 * @return	string		$result_xml
 *
 * @depends				class:	YFYM_Get_Paired_Tag
 *						methods: add_skip_reason
 *						functions: 
 */

trait YFYM_T_Simple_Get_Offer_Tag {
	public function get_offer_tag($tag_name = 'offer', $result_xml = '') {
		$product = $this->product;
		
		$offer_type = '';
		$offer_type = apply_filters('yfym_offer_type_filter', $offer_type, $this->get_feed_category_id(), $product->get_id(), $product, $this->get_feed_id());  /* изменён с версии 3.3.3 */	   

		$append_offer_tag = '';
		if (get_post_meta($product->get_id(), 'yfym_bid', true) !== '') {
			$yfym_bid = get_post_meta($product->get_id(), 'yfym_bid', true);
			$append_offer_tag = 'bid="'.$yfym_bid.'"';
		}

		$yfym_on_demand = yfym_optionGET('yfym_on_demand', $this->get_feed_id(), 'set_arr');
		if ($yfym_on_demand === 'enabled' && $product->get_stock_status() === 'onbackorder') {
			$append_offer_tag .= ' type="on.demand"';
		}

		/* с версии 2.1.2 */ 
		$append_offer_tag = apply_filters('yfym_append_offer_tag_filter', $append_offer_tag, $product, $this->get_feed_id());

		$offer_id_value = $product->get_id();
		$res_id_value = '';
		$yfym_source_id = yfym_optionGET('yfym_source_id', $this->get_feed_id(), 'set_arr');
		switch ($yfym_source_id) { 
			case "sku": 
				$res_id_value = $product->get_sku();
			break;
			case "post_meta": 				
				$yfym_source_id_post_meta_id = yfym_optionGET('yfym_source_id_post_meta', $this->get_feed_id(), 'set_arr');
				$yfym_source_id_post_meta_id = trim($yfym_source_id_post_meta_id);
				if (get_post_meta($product->get_id(), $yfym_source_id_post_meta_id, true) !== '') {
					$res_id_value = get_post_meta($product->get_id(), $yfym_source_id_post_meta_id, true);
				}
			break;	
			case "germanized":
				if (class_exists('WooCommerce_Germanized')) {
					if (get_post_meta($product->get_id(), '_ts_gtin', true) !== '') {
						$res_id_value = get_post_meta($product->get_id(), '_ts_gtin', true);
					}
				}
			break;
			default: $res_id_value = $offer_id_value;
		}
		if (!empty($res_id_value)) {$offer_id_value = $res_id_value;};

		$offer_id_yml = 'id="'.$offer_id_value.'"';
		$offer_id_yml = apply_filters('yfym_simple_offer_id_yml_filter', $offer_id_yml, array($product->get_id(), $product), $this->get_feed_id());

		if ($product->get_manage_stock() == true) { // включено управление запасом
			if ($product->get_stock_quantity() > 0) {
				$available = 'true';
			} else {
				if ($product->get_backorders() === 'no') { // предзаказ запрещен
					$available = 'false';
				} else {
					$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $this->get_feed_id(), 'set_arr');
					if ($yfym_behavior_onbackorder === 'false') {
						$available = 'false';
					} else {
						$available = 'true';
					}
				}
			}
	 } else { // отключено управление запасом
			if ($product->get_stock_status() === 'instock') {
				$available = 'true';
			} else if ($product->get_stock_status() === 'outofstock') { 
				$available = 'false';
			} else {
				$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $this->get_feed_id(), 'set_arr');
				if ($yfym_behavior_onbackorder === 'false') {
					$available = 'false';
				} else {
					$available = 'true';
				}
			}
		}
		$available = apply_filters('yfym_available_filter', $available, $product, $product->get_id(), $this->get_feed_id()); /* С версии 3.5.3 */		

		$available_yml = ' available="'.$available.'" ';
		$available_yml = apply_filters('yfym_simple_available_yml_filter', $available_yml, $product, $this->get_feed_id());
		$result_xml = '<offer '.$offer_id_yml.$available_yml.$append_offer_tag.' '.$offer_type.'>'.PHP_EOL;
		do_action('yfym_prepend_simple_offer');

		$result_xml = apply_filters('y4ym_f_simple_tag_offer', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;	
	}
}