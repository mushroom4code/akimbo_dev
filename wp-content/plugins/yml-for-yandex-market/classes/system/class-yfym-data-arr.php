<?php if (!defined('ABSPATH')) {exit;}
/**
 * Set and Get the Plugin Data
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			0.1.0
 * 
 * @version			0.1.0 (29-05-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param
 *
 * @return			
 *
 * @depends			classes:	
 *					traits:		
 *					methods:	
 *					functions:	
 *					constants:	
 */

class YFYM_Data_Arr {
	private $data_arr = [
		array('yfym_status_sborki', '-1', 'private'),
		array('yfym_date_sborki', '0000000001', 'private'), // дата начала сборки
		array('yfym_date_sborki_end', '0000000001', 'private'), // дата завершения сборки
		array('yfym_date_save_set', '0000000001', 'private'), // дата сохранения настроек плагина
		array('yfym_count_products_in_feed', '-1', 'private'), // число товаров, попавших в фид
		array('yfym_file_url', '', 'private'),
		array('yfym_file_file', '', 'private'),
		array('yfym_errors', '', 'private'),
		array('yfym_status_cron', 'off', 'private'),

		array('yfym_step_export', '500', 'public'),
		array('yfym_cache', 'disabled', 'public'),
		array('yfym_type_sborki', 'yml', 'public'), // тип собираемого файла yml или xls
		array('yfym_file_ids_in_yml', '', 'public'),
		array('yfym_ufup', '0', 'public'),
		array('yfym_magazin_type', 'woocommerce', 'public'), // тип плагина магазина 
		array('yfym_vendor', 'disabled', 'public'), 
		array('yfym_vendor_post_meta', '', 'public'), 

		array('yfym_whot_export', 'all', 'public'), // что выгружать (все или там где галка)
		array('yfym_yml_rules', 'yandex_market', 'public'),
		array('yfym_skip_missing_products', '0', 'public'),	
		array('yfym_separator_type', 'type1', 'public'), 
		array('yfym_behavior_onbackorder', 'false', 'public'), 
		array('yfym_behavior_stip_symbol', 'default', 'public'), 
		array('yfym_feed_assignment', '', 'public'),
		array('yfym_file_extension', 'xml', 'public'),
		array('yfym_archive_to_zip', 'disabled', 'public'),
		array('yfym_format_date', 'rfc_short', 'public'),

		array('yfym_shop_sku', 'disabled', 'public'),
		array('yfym_count', 'disabled', 'public'),
		array('yfym_auto_disabled', 'disabled', 'public'),
		array('yfym_amount', 'disabled', 'public'),
		array('yfym_manufacturer', 'disabled', 'public'),	

		array('yfym_currencies', 'enabled', 'public'),
		array('yfym_main_product', 'other', 'public'),		
		array('yfym_adult', 'no', 'public'),
		array('yfym_wooc_currencies', '', 'public'),
		array('yfym_desc', 'fullexcerpt', 'public'),
//		array('yfym_del_all_attributes', 'disabled', 'public'),
		array('yfym_enable_tags_custom', '', 'public'),
		array('yfym_enable_tags_behavior', 'default', 'public'),
		array('yfym_the_content', 'enabled', 'public'),
		array('yfym_replace_domain', '', 'public'),
		array('yfym_var_desc_priority', 'on', 'public'),
		array('yfym_clear_get', 'no', 'public'),
		array('yfym_price_from', 'no', 'public'), // разрешить "цена от"
		array('yfym_oldprice', 'no', 'public'),
		array('yfym_vat', 'disabled', 'public'),
		array('yfym_behavior_of_params', 'default', 'public'),
		//		'yfym_params_arr', serialize([ ]),
		//		'yfym_add_in_name_arr', serialize([ ]),
		//		'yfym_no_group_id_arr', serialize([ ]),
/* ? */	array('yfym_product_tag_arr', '', 'public'), // id меток таксономии product_tag
		array('yfym_store', 'false', 'public'),
		array('yfym_condition', 'disabled', 'public'),
		array('yfym_reason', '', 'public'),
		array('yfym_quality', 'perfect', 'public'),
		array('yfym_delivery', 'false', 'public'),
		array('yfym_pickup_options', '0', 'public'),
		array('yfym_pickup_cost', '0', 'public'),
		array('yfym_pickup_days', '32', 'public'),
		array('yfym_pickup_order_before', '', 'public'),
		array('yfym_delivery_options', '0', 'public'),
		array('yfym_delivery_cost', '0', 'public'),
		array('yfym_delivery_days', '32', 'public'),
		array('yfym_order_before', '', 'public'),
		array('yfym_delivery_options2', '0', 'public'),
		array('yfym_delivery_cost2', '0', 'public'),
		array('yfym_delivery_days2', '32', 'public'),
		array('yfym_order_before2', '', 'public'),		
		array('yfym_sales_notes_cat', 'off', 'public'),
		array('yfym_sales_notes', '', 'public'),
		array('yfym_model', 'disabled', 'public'), // атрибут model магазина
		array('yfym_pickup', 'true', 'public'),
		array('yfym_barcode', 'disabled', 'public'),
		array('yfym_barcode_post_meta', '', 'public'),
		array('yfym_barcode_post_meta_var', '', 'public'),
		array('yfym_vendorcode', 'disabled', 'public'),
		array('yfym_cargo_types', 'disabled', 'public'),
		array('yfym_enable_auto_discount', '', 'public'),
		array('yfym_expiry', 'off', 'public'),
		array('yfym_period_of_validity_days', 'disabled', 'public'),
		array('yfym_downloadable', 'off', 'public'),
		array('yfym_age', 'off', 'public'),	
		array('yfym_country_of_origin', 'off', 'public'),
		array('yfym_source_id', 'disabled', 'public'),
		array('yfym_source_id_post_meta', '', 'public'),
		array('yfym_on_demand', 'disabled', 'public'),
		array('yfym_ebay_stock', '0', 'public'), 
		array('yfym_manufacturer_warranty', 'off', 'public'),
		array('yfym_enable_auto_discounts', '', 'public'),
		array('yfym_skip_backorders_products', '0', 'public'),
		array('yfym_no_default_png_products', '0', 'public'),	
		array('yfym_skip_products_without_pic', '0', 'public'),	
		array('yfym_warehouse', 'Основной склад', 'public'),	
	];

	public function __construct($blog_title = '', $currency_id_xml = '', $data_arr = [ ]) {
		if (empty($blog_title)) {
			$blog_title = mb_strimwidth(get_bloginfo('name'), 0, 20);
			$this->blog_title = $blog_title;
		}
		if (empty($currency_id_xml)) {
			if (class_exists('WooCommerce')) {
				$currency_id_xml = get_woocommerce_currency();
			} else {
				$currency_id_xml = 'USD';
			}
			$this->currency_id_xml = $currency_id_xml;
		}
		if (!empty($data_arr)) {
			$this->data_arr = $data_arr;
		}
		
		array_push($this->data_arr,
			array('yfym_shop_name', $this->blog_title, 'public'),
			array('yfym_company_name', $this->blog_title, 'public')
		);

		$args_arr = array($this->blog_title, $this->currency_id_xml);
		$this->data_arr = apply_filters('yfym_set_default_feed_settings_result_arr_filter', $this->data_arr, $args_arr);
	}

	public function get_data_arr() {
		return $this->data_arr;
	}

	// @return array([0] => opt_key1, [1] => opt_key2, ...)
	public function get_opts_name($whot = '') {
		if ($this->data_arr) {
			$res_arr = [ ];		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					default:
						$res_arr[] = $this->data_arr[$i][0];
				}
			}
			return $res_arr;
		} else {
			return [ ];
		}
	}

	// @return array(opt_name1 => opt_val1, opt_name2 => opt_val2, ...)
	public function get_opts_name_and_def_date($whot = 'all') {
		if ($this->data_arr) {
			$res_arr = [ ];		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					default:
						$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
				}
			}
			return $res_arr;
		} else {
			return [ ];
		}
	}

	public function get_opts_name_and_def_date_obj($whot = 'all') {		
		$source_arr = $this->get_opts_name_and_def_date($whot);

		$res_arr = [ ];	
		foreach($source_arr as $key => $value) {
			$res_arr[] = new YFYM_Data_Arr_Helper($key, $value); // return unit obj
		}
		return $res_arr;
	}
}
class YFYM_Data_Arr_Helper {	
	private $opt_name;
	private $opt_def_value;

	function __construct($name = '', $def_value = '') {
		$this->opt_name = $name;
		$this->opt_def_value = $def_value;
	}

	function get_name() {
		return $this->opt_name;
	}

	function get_value() {
		return $this->opt_def_value;
	}
}