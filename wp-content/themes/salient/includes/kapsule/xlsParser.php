<?php

use Shuchkin\SimpleXLS;

/**
 * @param $filePath
 *
 * @return void
 */
function parseWithAppendKapsuls( $filePath ): void {
	$xls = SimpleXLS::parse( $filePath );

	if ( $xls !== false ) {
		$sheets          = $xls->sheets;
		$resultForAppend = treatmentKapsule( $sheets[0], $sheets[1] );
		if ( ! empty( $resultForAppend ) ) {
			foreach ( $resultForAppend as $item ) {
				$content = generateContentInPagekapsul( $item );
//		todo		if ( ! empty( $content )  &&  $item['name'] === 'Осень Индиго') {
				if ( ! empty( $content )) {
					wp_insert_post( wp_slash( [
						'post_title'   => $item['name'],
						'post_content' => $content,
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'ping_status'  => get_option( 'default_ping_status' ),
						'post_parent'  => 0,
					] ) );
				}
			}
		}

		wp_delete_file( $filePath );
	}
}

/**
 * @param $kapsuls
 * @param $products
 *
 * @return array
 */
function treatmentKapsule( $kapsuls, $products ): array {
	$kapsulsData = $resultData = $content = [];

	foreach ( $kapsuls['cells'] as $key => $kapsula ) {
		if ( $key === 0 ) {
			continue;
		}
		$kapsulsData[ $kapsula[0] ]['index']       = $kapsula[0];
		$kapsulsData[ $kapsula[0] ]['name']        = $kapsula[1];
		$kapsulsData[ $kapsula[0] ]['description'] = $kapsula[2];
	}
	if ( ! empty( $kapsulsData ) ) {
		$resultData = appendProductInKapsuls( $products, $kapsulsData );
	}

	return $resultData;
}

/**
 * @param $productsData
 * @param $kapsulsData
 *
 * @return array
 */
function appendProductInKapsuls( $productsData, $kapsulsData ): array {

	foreach ( $productsData['cells'] as $key => $product ) {
		if ( $key === 0 ) {
			continue;
		}

		if ( isset( $kapsulsData[ $product[0] ] ) && $kapsulsData[ $product[0] ]['name'] === $product[2] ) {
			$productData = get_posts( [
				'post_status' => 'publish',
				'post_type'   => 'product',
				'numberposts' => 1,
				'meta_key'    => '_sku',
				'meta_value'  => trim( $product[1] )
			] );
			if ( ! empty( $productData ) && $productData[0]->post_status === 'publish' ) {
				$productForData['name']                                             = get_the_title( $productData[0]->ID );
				$productForData['url']                                              = $productData[0]->guid;
				$productForData['ID']                                               = $productData[0]->ID;
				$productForData['price']                                            = get_post_meta( $productData[0]->ID, '_price', 1 );
				$productForData['planned_date']                                     = get_post_meta( $productData[0]->ID, 'planned_date', 1 );
				$kapsulsData[ $product[0] ]['products_list'][ $productData[0]->ID ] = $productForData;
			}
		}

	}

	return $kapsulsData;
}

/**
 * @param $Data
 *
 * @return string
 */
function generateContentInPagekapsul( $Data ): string {
//	name kapsul
	$content = '[vc_row type="in_container" full_screen_row_position="middle" scene_position="center"
	text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" bg_image_animation="none"]
	[vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" 
	background_hover_color_opacity="1" column_link_target="_self" column_shadow="none" column_border_radius="none"
	width="1/1" tablet_width_inherit="default" tablet_text_alignment="default" phone_text_alignment="default"
	column_border_width="none" column_border_style="solid" bg_image_animation="none"]
	[nectar_highlighted_text style="full_text"]
	<h3>' . $Data['name'] . '</h3>
	[/nectar_highlighted_text]
	[/vc_column]
	[/vc_row]';
//	description kapsul
	$content .= '[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" 
	text_align="left" overlay_strength="0.3" shape_divider_position="bottom" bg_image_animation="none"]
	[vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1"
	 background_hover_color_opacity="1" column_link_target="_self" column_shadow="none" column_border_radius="none" width="1/1"
	tablet_width_inherit="default" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" 
	column_border_style="solid" bg_image_animation="none"][nectar_highlighted_text style="full_text"]' . $Data['description'] . '[/nectar_highlighted_text]
	[/vc_column]
	[/vc_row]';

// product list
	$content .= '[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" 
			text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" bg_image_animation="none"]';
	$i       = 0;
	foreach ( $Data['products_list'] as $product ) {
		$i ++;
		$item = '[vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" 
		background_hover_color_opacity="1" column_link_target="_self" column_shadow="none" column_border_radius="none"
		width="1/4" tablet_width_inherit="default" tablet_text_alignment="default" phone_text_alignment="default"
		column_border_width="none" column_border_style="solid" bg_image_animation="none"
		column_link="' . $product['url'] . '"]
		[image_with_animation image_url="173342" alignment="" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"]
		[nectar_food_menu_item style="default" item_name_heading_tag="h5" item_name="' . $product['name'] . '" item_price="' . $product['price'] . ' ₽"]
		[/vc_column]';
		if ( ( $i % 4 ) == 0 ) {
			$item .= '[/vc_row]
			[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" 
			text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" bg_image_animation="none"]';
		}
		$content .= $item;
	}

	$content .= '[/vc_row]';

	return $content;
}