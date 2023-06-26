<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Traits for variable products
 *
 * @author		Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @since		1.0.0
 *
 * @return 		$result_xml (string)
 *
 * @depends		class:		YFYM_Get_Paired_Tag
 *				methods: 	get_product
 *							get_offer
 *							get_feed_id
 *				functions:	yfym_optionGET
 *				variable:	feed_category_id (set it)
 */

trait YFYM_T_Common_Get_CatId {
	public function get_catid( $catid = null ) {
		$product = $this->get_product();
		// "Категории 	  
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$catWPSEO = new WPSEO_Primary_Term( 'product_cat', $product->get_id() );
			$catidWPSEO = $catWPSEO->get_primary_term();
			if ( $catidWPSEO !== false ) {
				$catid = $catidWPSEO;
			} else {
				$termini = get_the_terms( $product->get_id(), 'product_cat' );
				if ( $termini !== false ) {
					foreach ( $termini as $termin ) {
						$catid = $termin->term_id;
						break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
					}
				} else { // если база битая. фиксим id категорий
					new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
					$product_cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( "fields" => "ids" ) );
					// Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
					// wp_set_object_terms($product->get_id(), $product_cats, 'product_cat');
					if ( is_array( $product_cats ) && count( $product_cats ) ) {
						$catid = $product_cats[0];
						new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' база наверняка битая. wp_get_post_terms вернула массив. $catid = ' . $catid . '; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
					}
				}
			}
		} else if ( class_exists( 'RankMath' ) ) {
			$primary_cat_id = get_post_meta( $product->get_id(), 'rank_math_primary_category', true );
			if ( $primary_cat_id ) {
				$product_cat = get_term( $primary_cat_id, 'product_cat' );
				$catid = $product_cat->term_id;
			} else {
				$termini = get_the_terms( $product->get_id(), 'product_cat' );
				if ( $termini !== false ) {
					foreach ( $termini as $termin ) {
						$catid = $termin->term_id;
						break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
					}
				} else { // если база битая. фиксим id категорий
					new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
					$product_cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( "fields" => "ids" ) );
					// Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
					// wp_set_object_terms($product->get_id(), $product_cats, 'product_cat');
					if ( is_array( $product_cats ) && count( $product_cats ) ) {
						$catid = $product_cats[0];
						new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' база наверняка битая. wp_get_post_terms вернула массив. $catid = ' . $catid . '; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
					}
				}
			}
		} else {
			$termini = get_the_terms( $product->get_id(), 'product_cat' );
			if ( $termini !== false ) {
				foreach ( $termini as $termin ) {
					$catid = $termin->term_id;
					break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
				}
			} else { // если база битая. фиксим id категорий
				new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
				$product_cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( "fields" => "ids" ) );
				// Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
				// wp_set_object_terms($product->get_id(), $product_cats, 'product_cat');
				if ( is_array( $product_cats ) && count( $product_cats ) ) {
					$catid = $product_cats[0];
					new YFYM_Error_Log( 'FEED № ' . $this->get_feed_id() . '; WARNING: Для товара $product->get_id() = ' . $product->get_id() . ' база наверняка битая. wp_get_post_terms вернула массив. $catid = ' . $catid . '; Файл: trait-yfym-t-common-get-catid.php; Строка: ' . __LINE__ );
				}
			}
		}

		if ( $catid == '' ) {
			$this->add_skip_reason( array( 'reason' => __( 'The product has no categories', 'yml-for-yandex-market' ), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-get-catid.php', 'line' => __LINE__ ) );
			return '';
		}

		$this->feed_category_id = $catid;
		return $catid;
	}
}