<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Traits Age for simple products
 *
 * @package					YML for Yandex Market
 * @subpackage				
 * @since					0.1.0
 * 
 * @version					3.11.0 (15-06-2023)
 * @author					Maxim Glazunov
 * @link					https://icopydoc.ru/
 * @see						
 *
 * @depends					classes:	YFYM_Get_Paired_Tag
 *							traits:		
 *							methods:	get_product
 *										get_feed_id
 *							functions:	common_option_get
 *							constants:	
 */

trait YFYM_T_Simple_Get_Age {
	/**
	 * Summary of get_age
	 * 
	 * @param string $tag_name (not require)
	 * @param string $result_xml (not require)
	 * 
	 * @return string
	 */
	public function get_age( $tag_name = 'age', $result_xml = '' ) {
		$tag_value = '';

		$age = common_option_get( 'yfym_age', false, '1', 'yfym' );
		if ( empty( $age ) || $age === 'disabled' ) {

		} else {
			$age = (int) $age;
			$tag_value = $this->get_product()->get_attribute( wc_attribute_taxonomy_name_by_id( $age ) );
		}

		$tag_value = apply_filters(
			'y4ym_f_simple_tag_value_age',
			$tag_value,
			[ 
				'product' => $this->get_product()
			],
			$this->get_feed_id()
		);

		if ( ! empty( $tag_value ) ) {

			$tag_name = apply_filters(
				'y4ym_f_simple_tag_name_age',
				$tag_name,
				[ 
					'product' => $this->get_product()
				],
				$this->get_feed_id()
			);

			$result_xml = new YFYM_Get_Paired_Tag( $tag_name, $tag_value );
		}

		$result_xml = apply_filters(
			'y4ym_f_simple_tag_age',
			$result_xml,
			[ 
				'product' => $this->get_product()
			],
			$this->get_feed_id()
		);

		return $result_xml;
	}
}