<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Barcode for simple products
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
*/

trait YFYM_T_Simple_Get_Barcode {
	public function get_barcode($tag_name = 'barcode', $result_xml = '') {
		$product = $this->get_product();
		$tag_value = '';
		 
		$yfym_barcode = yfym_optionGET('yfym_barcode', $this->get_feed_id(), 'set_arr');
		switch ($yfym_barcode) { /* disabled, sku, post_meta, germanized, id */
			case "disabled": // выгружать штрихкод нет нужды		
				break; 
			case "sku": // выгружать из артикула
				$tag_value = $product->get_sku();
				break;
			case "post_meta":
				$barcode_post_meta_id = yfym_optionGET('yfym_barcode_post_meta', $this->get_feed_id(), 'set_arr');
				$barcode_post_meta_id = trim($barcode_post_meta_id);
				if (get_post_meta($product->get_id(), $barcode_post_meta_id, true) !== '') {
					$tag_value = get_post_meta($product->get_id(), $barcode_post_meta_id, true);
				}
				break;
			case "germanized":
				if (class_exists('WooCommerce_Germanized')) {
					if (get_post_meta($product->get_id(), '_ts_gtin', true) !== '') {
						$tag_value = get_post_meta($product->get_id(), '_ts_gtin', true);
					}
				}
				break;
			case "ean-for-woocommerce":
				if (class_exists('Alg_WC_EAN')) {
					if (get_post_meta($product->get_id(), '_alg_ean', true) !== '') {
						$tag_value = get_post_meta($product->get_id(), '_alg_ean', true);
					}
				}
				break;
			default:
				$tag_value = apply_filters('y4ym_f_simple_tag_value_switch_barcode', $tag_value, array('product' => $product, 'switch_value' => $yfym_barcode), $this->get_feed_id());			
				if ($tag_value == '') {
					$yfym_barcode = (int)$yfym_barcode;
					$tag_value = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_barcode));			
				}
		}

		$tag_value = apply_filters('y4ym_f_simple_tag_value_barcode', $tag_value, array('product' => $product), $this->get_feed_id());
		if (!empty($tag_value)) {
			$tag_name = apply_filters('y4ym_f_simple_tag_name_barcode', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_barcode', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>