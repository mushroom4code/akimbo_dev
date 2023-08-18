<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get unit for Simple Products 
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			0.1.0
 * 
 * @version			1.0.0 (25-05-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param		
 *
 * @return		
 *
 * @depends			classes:	YFYM_Get_Unit_Offer
 *					traits:		
 *					methods:	
 *					functions:	
 *					constants:	
 *					options:	
 *
 */

class YFYM_Get_Unit_Offer_Simple extends YFYM_Get_Unit_Offer {
	use YFYM_T_Common_Get_CatId;
	use YFYM_T_Common_Skips;

	use YFYM_T_Simple_Get_Age;
	use YFYM_T_Simple_Get_Amount;
	use YFYM_T_Simple_Get_Barcode;
	use YFYM_T_Simple_Get_Cargo_Types;
	use YFYM_T_Simple_Get_CategoryId;
	use YFYM_T_Simple_Get_Condition;
	use YFYM_T_Simple_Get_Count;
	use YFYM_T_Simple_Get_Country_Of_Orgin;
	use YFYM_T_Simple_Get_Credit_Template;
	use YFYM_T_Simple_Get_Currencyid;
	use YFYM_T_Simple_Get_Delivery_Options;
	use YFYM_T_Simple_Get_Delivery;
	use YFYM_T_Simple_Get_Description;
	use YFYM_T_Simple_Get_Dimensions;
	use YFYM_T_Simple_Get_Disabled;
	use YFYM_T_Simple_Get_Downloadable;
	use YFYM_T_Simple_Get_Enable_Auto_Discounts;
	use YFYM_T_Simple_Get_Expiry;
	use YFYM_T_Simple_Get_Id;
	use YFYM_T_Simple_Get_Instock;
	use YFYM_T_Simple_Get_Keywords;
	use YFYM_T_Simple_Get_Manufacturer_Warranty;
	use YFYM_T_Simple_Get_Manufacturer;
	use YFYM_T_Simple_Get_Market_Sku;
	use YFYM_T_Simple_Get_Min_Price;
	use YFYM_T_Simple_Get_Min_Quantity;
	use YFYM_T_Simple_Get_Model;
	use YFYM_T_Simple_Get_Name;
	use YFYM_T_Simple_Get_Offer_Tag;
	use YFYM_T_Simple_Get_Outlets;
	use YFYM_T_Simple_Get_Params;
	use YFYM_T_Simple_Get_Period_Of_Validity_Days;
	use YFYM_T_Simple_Get_Pickup_Options;
	use YFYM_T_Simple_Get_Pickup;
	use YFYM_T_Simple_Get_Picture;
	use YFYM_T_Simple_Get_Premium_Price;
	use YFYM_T_Simple_Get_Price;
	use YFYM_T_Simple_Get_Price_Rrp;
	use YFYM_T_Simple_Get_Recommend_Stock_Data;
	use YFYM_T_Simple_Get_Sales_Notes;
	use YFYM_T_Simple_Get_Shop_Sku;
	use YFYM_T_Simple_Get_Step_Quantity;
	use YFYM_T_Simple_Get_Store;
	use YFYM_T_Simple_Get_Supplier;
	use YFYM_T_Simple_Get_Tn_Ved_Codes;
	use YFYM_T_Simple_Get_Url;
	use YFYM_T_Simple_Get_Vat;
	use YFYM_T_Simple_Get_Vendor;
	use YFYM_T_Simple_Get_Vendorcode;
	use YFYM_T_Simple_Get_Weight;

	public function generation_product_xml( $result_xml = '' ) {
		$this->feed_category_id = $this->get_catid();
		$this->get_skips();

		$yfym_yml_rules = yfym_optionGET( 'yfym_yml_rules', $this->feed_id, 'set_arr' );
		switch ( $yfym_yml_rules ) {
			case "yandex_market":
				$result_xml = $this->adv();
				break;
//          Enterego
            case "group_price":
                $result_xml = $this->group_price();
                break;
			case "yandex_webmaster":
				$result_xml = $this->yandex_webmaster();
				break;
			case "products_and_offers":
				$result_xml = $this->products_and_offers();
				break;
			case "single_catalog":
				$result_xml = $this->single_catalog();
				break;
			case "dbs":
				$result_xml = $this->dbs();
				break;
			case "sales_terms":
				$result_xml = $this->sales_terms();
				break;
			case "beru":
				$result_xml = $this->old();
				break;
			case "all_elements":
				$result_xml = $this->all_elements();
				break;
			case "ozon":
				$result_xml = $this->ozon();
				break;
			case "sbermegamarket":
				$result_xml = $this->sbermegamarket();
				break;
			case "vk":
				$result_xml = $this->vk();
				break;
			default:
				$result_xml = $this->all_elements();
		}

		$result_xml = apply_filters( 'yfym_append_simple_offer_filter', $result_xml, $this->product, $this->feed_id );
		$result_xml = apply_filters(
			'y4ym_f_append_simple_offer',
			$result_xml,
			[ 
				'product' => $this->product,
				'feed_category_id' => $this->get_feed_category_id()
			],
			$this->feed_id
		);

		if ( $yfym_yml_rules !== 'group_price' ) {
			$result_xml .= '</offer>' . PHP_EOL;
		}

		return $result_xml;
	}

	private function adv( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();
		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

    private function group_price($result_xml = '')
    {
        $addProduct = get_post_meta($this->get_product()->id, 'group_price_unload_disabled', true);
	    $quantity_off = (int)get_post_meta( $this->get_offer()->get_id(), '_stock',true)  ?? 0;
	    $backorders_count = (int)get_post_meta( $this->get_offer()->get_id(), '_backorders_count', true) ?? 0;
	    $quantity = ( $quantity_off - $backorders_count ) ?? 0;
	    $size = get_post_meta($this->get_offer()->get_id(), 'attribute_pa_razmer', true);
	    if ( $addProduct !== 'true' && $quantity > 1 && !empty($size)) {
            $result_xml .= $this->get_offer_tag();
            $result_xml .= $this->get_disabled();
            $result_xml .= $this->get_params();
            $result_xml .= $this->get_name();
            $result_xml .= $this->get_enable_auto_discounts();
            $result_xml .= $this->get_description();
            $result_xml .= $this->get_picture();
            $result_xml .= $this->get_url();
            if (class_exists('WOOCS')) {
                $yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
                if ($yfym_wooc_currencies !== '') {
                    global $WOOCS;
                    $WOOCS->set_currency($yfym_wooc_currencies);
                }
            }
            $p = $this->get_price();
            if ($p !== '') {
                $result_xml .= $p;
                $result_xml .= $this->get_currencyid();
            }
            if (class_exists('WOOCS')) {
                global $WOOCS;
                $WOOCS->reset_currency();
            }

//         enterego
		    $color = $this->get_product()->get_attribute('pa_tsvet-tkani');
		    if ( empty( $color ) ) {
			    $color = $this->get_product()->get_attribute('pa_tsvet');
		    }

		    $barcode = get_post_meta($this->get_offer()->get_id(), 'barcode', true);
	        $image_ids = get_post_meta( $this->get_product()->id, '_product_image_gallery', true );
	        if ( ! empty( $image_ids ) ) {
		        $image_ids = explode( ',', $image_ids );
		        foreach ( $image_ids as $image_id ) {
			        $thumb_url  = wp_get_attachment_image_src( $image_id, 'full', true );
			        $thumb_yml  = $thumb_url[0]; /* урл оригинал миниатюры товара */
			        $result_xml .= '<picture>' . get_from_url( $thumb_yml ) . '</picture>' . PHP_EOL;
		        }
	        }
//			enterego
            $name = explode(' ', $this->get_product()->get_title());
            $result_xml .= '<typePrefix>' . $name[0] . '</typePrefix>' . PHP_EOL;
            $result_xml .= $this->get_expiry();
            $result_xml .= $this->get_age();
            $result_xml .= $this->get_downloadable();
            $result_xml .= $this->get_sales_notes();
            $result_xml .= $this->get_manufacturer_warranty();
            $result_xml .= $this->get_vendor();
            $result_xml .= $this->get_weight();
//			enterego
	        $result_xml .= '<param name="Цвет">' . $color . '</param>'.PHP_EOL;
            $result_xml .= $this->get_dimensions();
            $result_xml .= '<model>' . $this->get_offer()->get_title() . '</model>' . PHP_EOL;
            $result_xml .= '<variant>' . PHP_EOL;
            $result_xml .= $this->get_amount();
            $result_xml .= '<size name="Размер">' . $size . '</size>' . PHP_EOL;
            $result_xml .= '<quantity>' . $quantity . '</quantity>' . PHP_EOL;
            $result_xml .= '<barcode>' . $barcode . '</barcode>' . PHP_EOL;
            $result_xml .= '</variant>' . PHP_EOL;
//	        enterego
            $result_xml .= $this->get_vendorcode();
            $result_xml .= $this->get_store();
            $result_xml .= $this->get_pickup();
            $result_xml .= $this->get_delivery();
            $result_xml .= $this->get_categoryid();
            $result_xml .= $this->get_vat();
            $result_xml .= $this->get_delivery_options();
            $result_xml .= $this->get_pickup_options();
            $result_xml .= $this->get_condition();
            $result_xml .= $this->get_credit_template();
            $result_xml .= $this->get_supplier();
            $result_xml .= $this->get_min_quantity();
            $result_xml .= $this->get_step_quantity();
		    $result_xml .= '</offer>' . PHP_EOL;
        }

        return $result_xml;
    }

	private function single_catalog( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

	private function dbs( $result_xml = '' ) {
		//	https://yandex.ru/support/marketplace/assortment/files/index.html
		//	https://yandex.ru/support/marketplace/tools/elements/offer-general.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

	private function sales_terms( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();

		$result_xml .= $this->get_url();
		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_count();

		return $result_xml;
	}

	private function old( $result_xml = '' ) {
		$result_xml .= $this->all_elements();

		return $result_xml;
	}

	private function sbermegamarket( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options( 'shipment-options', '', 'sbermegamarket' );
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_expiry();
		//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_outlets( 'outlets', '', 'sbermegamarket' );
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_premium_price();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function vk( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();

		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_instock();

		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function all_elements( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_expiry();
		//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_premium_price();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_price_rrp();
		}
		$result_xml .= $this->get_currencyid();
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function ozon( $result_xml = '' ) {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$p = $this->get_price();
		if ( $p !== '' ) {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}
		$result_xml .= $this->get_min_price();

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		// $result_xml .= $this->get_cargo_types	();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		$result_xml .= $this->get_premium_price();
		$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_tn_ved_codes();

		return $result_xml;
	}

	private function yandex_webmaster( $result_xml = '' ) {
		// https://yandex.ru/support/products/features.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		//	$result_xml .= $this->get_age();
		//	$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		//	$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		//	$result_xml .= $this->get_downloadable();
		//	$result_xml .= $this->get_enable_auto_discounts();
		//	$result_xml .= $this->get_expiry();
		//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		//	$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		//	$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		// $result_xml .= $this->get_premium_price();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$result_xml .= $this->get_price();
		/*$p = $this->get_price();
					if ($p !== '') {
						$result_xml .= $p;
						$result_xml .= $this->get_price_rrp();
					}*/
		$result_xml .= $this->get_currencyid();
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		//	$result_xml .= $this->get_store();
		//	$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		//	$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function products_and_offers( $result_xml = '' ) {
		// https://yandex.ru/support/products/partners.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		//	$result_xml .= $this->get_age();
		//	$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		//	$result_xml .= $this->get_count();
		//	$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		//	$result_xml .= $this->get_downloadable();
		//	$result_xml .= $this->get_enable_auto_discounts();
		//	$result_xml .= $this->get_expiry();
		//	$result_xml .= $this->get_group_id();
		//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		//	$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		//	$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		//	$result_xml .= $this->get_pickup_options();
		//	$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		// $result_xml .= $this->get_premium_price();

		if ( class_exists( 'WOOCS' ) ) {
			$yfym_wooc_currencies = yfym_optionGET( 'yfym_wooc_currencies', $this->feed_id, 'set_arr' );
			if ( $yfym_wooc_currencies !== '' ) {
				global $WOOCS;
				$WOOCS->set_currency( $yfym_wooc_currencies );
			}
		}
		$result_xml .= $this->get_price();
		/*$p = $this->get_price();
					if ($p !== '') {
						$result_xml .= $p;
						$result_xml .= $this->get_price_rrp();
					}*/
		$result_xml .= $this->get_currencyid();
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$WOOCS->reset_currency();
		}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		//	$result_xml .= $this->get_store();
		//	$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		//	$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}
}