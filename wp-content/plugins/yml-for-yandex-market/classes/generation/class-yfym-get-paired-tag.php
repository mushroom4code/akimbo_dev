<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Creates a paired tag 
 *
 * @link			https://icopydoc.ru/
 * @since		1.0.0
 *
 * Usage example:
 * new Get_Paired_Tag('price', 1500, array('from' => 'true'));
 */

class YFYM_Get_Paired_Tag extends YFYM_Get_Closed_Tag {
	protected $val_tag;
	protected $attr_tag_arr;

	public function __construct( $name_tag, $val_tag = '', array $attr_tag_arr = [] ) {
		parent::__construct( $name_tag );

		if ( ! empty( $val_tag ) ) {
			$this->val_tag = $val_tag;
		} else if ( $val_tag === (float) 0 || $val_tag === (int) 0 ) {
			// если нужно передать нулевое значение в качестве value
			$this->val_tag = $val_tag;
		}

		if ( ! empty( $attr_tag_arr ) ) {
			$this->attr_tag_arr = $attr_tag_arr;
		}
	}

	public function __toString() {
		if ( empty( $this->get_name_tag() ) ) {
			return '';
		} else {
			return sprintf( "<%1\$s%3\$s>%2\$s</%1\$s>",
				$this->get_name_tag(),
				$this->get_val_tag(),
				$this->get_attr_tag()
			) . PHP_EOL;
		}
	}

	public function get_val_tag() {
		return $this->val_tag;
	}

	public function get_attr_tag() {
		$res_string = '';
		if ( ! empty( $this->attr_tag_arr ) ) {
			foreach ( $this->attr_tag_arr as $key => $value ) {
				$res_string .= ' ' . $key . '="' . $value . '"';
			}
		}
		return $res_string;
	}
}