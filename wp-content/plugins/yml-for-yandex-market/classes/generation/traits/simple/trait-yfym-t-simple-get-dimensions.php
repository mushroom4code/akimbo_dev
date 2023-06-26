<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Dimensions for simple products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:
*				methods: 	get_product
*							get_feed_id
*				functions:	
*/

trait YFYM_T_Simple_Get_Dimensions {
	public function get_dimensions($tag_name = 'dimensions', $result_xml = '') {
		$product = $this->product;

		$dimensions = wc_format_dimensions($product->get_dimensions(false));
        if ($product->has_dimensions()) {
			$length_yml = $product->get_length();
			if (!empty($length_yml)) {$length_yml = round(wc_get_dimension($length_yml, 'cm'), 3);}
			   
			$width_yml = $product->get_width();
			if (!empty($width_yml)) {$width_yml = round(wc_get_dimension($width_yml, 'cm'), 3);}
			  
			$height_yml = $product->get_height();
			if (!empty($height_yml)) {$height_yml = round(wc_get_dimension($height_yml, 'cm'), 3);}		  
			   
			if (($length_yml > 0) && ($width_yml > 0) && ($height_yml > 0)) {
				$result_xml = '<dimensions>'.$length_yml.'/'.$width_yml.'/'.$height_yml.'</dimensions>'.PHP_EOL;
			}
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_dimensions', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>