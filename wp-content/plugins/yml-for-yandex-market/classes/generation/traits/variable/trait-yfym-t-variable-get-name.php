<?php if (!defined('ABSPATH')) {exit;}
/**
 * Traits Name for variable products
 *
 * @package					YML for Yandex Market
 * @subpackage				
 * @since					1.0.0 
 * 
 * @version					1.0.0 (29-03-2023)
 * @author					Maxim Glazunov
 * @link					https://icopydoc.ru/
 * @see						
 * 
 * @param	string			$tag_name (not require)
 * @param	string			$result_xml (not require)
 *
 * @return 					$result_xml (string)
 *
 * @depends					classes:	YFYM_Get_Paired_Tag
 *							traits:		
 *							methods:	get_product
 *										get_offer
 *										get_feed_id
 *							functions:	yfym_optionGET
 *							constants:	
 */

trait YFYM_T_Variable_Get_Name {
	public function get_name($tag_name = 'name', $result_xml = '') {
		$result_yml_name = $this->get_product()->get_title();
		$result_yml_name = apply_filters('y4ym_f_simple_tag_value_name', $result_yml_name, [ 'product' => $this->get_product(), 'offer' => $this->get_offer() ], $this->get_feed_id());
		$result_yml_name = apply_filters('yfym_change_name', $result_yml_name, $this->get_product()->get_id(), $this->get_product(), $this->get_feed_id());
		/* с версии 2.0.7 в фильтр добавлен параметр $this->get_product() */

		$result_yml_name = apply_filters('yfym_variable_change_name', $result_yml_name, $this->get_product()->get_id(), $this->get_product(), $this->get_offer(), $this->get_feed_id());
		$result_yml_name = apply_filters('y4ym_f_variable_tag_value_name', $result_yml_name, [ 'product' => $this->get_product(), 'offer' => $this->get_offer() ], $this->get_feed_id());

		// массив категорий для которых запрещен group_id
		$no_group_id_arr = unserialize(yfym_optionGET('yfym_no_group_id_arr', $this->get_feed_id()));
		if (empty($no_group_id_arr)) {
			$result_yml_name_itog = $result_yml_name;
		} else {
			// массив с group_id заполнен
			$сur_сategory_id = (string)$this->get_feed_category_id();
			// если id текущей категории совпал со списком категорий без group_id			  
			if (in_array($сur_сategory_id, $no_group_id_arr)) {
				$add_in_name_arr = unserialize(yfym_optionGET('yfym_add_in_name_arr', $this->get_feed_id()));
				$attributes = $this->get_product()->get_attributes(); // получили все атрибуты товара
				$param_at_name = '';		

				$separator_type = yfym_optionGET('yfym_separator_type', $this->get_feed_id(), 'set_arr');			 
				switch ($separator_type) {
					case "type1":
						$so = '('; $sz = ')';
						$sd = ': '; $sr = ',';
					break;
					case "type2":
						$so = '('; $sz = ')';
						$sd = ' - '; $sr = ',';
					break; 
					case "type3":
						$so = ''; $sz = '';
						$sd = ': '; $sr = ',';
					break;
					case "type4":
						$so = ''; $sz = '';
						$sd = ''; $sr = '';
					break;				
					default: 
						$so = ''; $sz = '';
						$sd = ': '; $sr = ',';
				}		 
			 
				foreach ($attributes as $param) {					
					if ($param->get_variation() == false) {
						// это обычный атрибут
						continue;
						$param_val = $this->get_product()->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
					} else { 
						// это атрибут вариации
						$param_val = $this->get_offer()->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
					}
					// если этот параметр не нужно выгружать - пропускаем
					$variation_id_string = (string)$param->get_id(); // важно, т.к. в настройках id как строки
					if (!in_array($variation_id_string, $add_in_name_arr, true)) {continue;}
					$param_name = wc_attribute_label(wc_attribute_taxonomy_name_by_id($param->get_id()));
					// если пустое имя атрибута или значение - пропускаем
					if (empty($param_name) || (empty($param_val) && $param_val !== '0')) {continue;}
					if ($separator_type === 'type4') {
						$param_at_name .= $sd.ucfirst(yfym_replace_decode($param_val)).$sr.' ';
					} else {
						$param_at_name .= $param_name.$sd.ucfirst(yfym_replace_decode($param_val)).$sr.' ';
					}
				}
				$param_at_name = trim($param_at_name);
				if ($param_at_name == '') {
					$this->add_skip_reason( [ 'offer_id' => $this->get_offer()->get_id(), 'reason' => __('There are no variable attributes to create a unique product name', 'yfym'), 'post_id' => $this->get_offer()->get_id(), 'file' => 'trait-yfym-t-variable-get-name.php', 'line' => __LINE__ ] ); return '';
				}

				// подрежем последнюю запятую/разделитель
				$lenght_sr = strlen($sr);
/* ! потенциально заменить на mb_strimwidth() */
				if ($lenght_sr > 0) {$param_at_name = substr($param_at_name, 0, -$lenght_sr);}

				if (!class_exists('YmlforYandexMarketPro')) {
					$result_yml_name_itog = $result_yml_name.' '.$so.$param_at_name.$sz;
				} else {
					// если не стоит галка выгрузки только первой вариации
					if (yfym_optionGET('yfymp_one_variable', $this->get_feed_id()) !== 'on') {
						$result_yml_name_itog = $result_yml_name.' '.$so.$param_at_name.$sz;
					}
				}
		
				$result_yml_name_itog = apply_filters('yfym_name_no_groupid_filter', $result_yml_name_itog, $result_yml_name, $this->get_product(), $so, $sz, $sd, $sr, $this->get_feed_id());
			} else {
				// совпадений нет. подставляем group_id
				$result_yml_name_itog = $result_yml_name;
			}
		}
		
		$result_yml_name_itog = apply_filters('yfym_before_insert_name_filter', $result_yml_name_itog, $this->get_feed_id()); /* с версии 3.3.18 */
		$result_xml = new YFYM_Get_Paired_Tag($tag_name, htmlspecialchars($result_yml_name_itog, ENT_NOQUOTES));

		$result_xml = apply_filters('y4ym_f_variable_tag_name', $result_xml,[ 'product' => $this->get_product(), 'offer' => $this->get_offer() ], $this->get_feed_id());
		return $result_xml;	
	}
}