<?php
use Shuchkin\SimpleXLS;

/**
 * @param $filePath
 * @return void
 */
function parseWithAppendKapsuls($filePath)
{
    $xls = SimpleXLS::parse($filePath);

    if ($xls !== false) {
        $sheets = $xls->sheets;

        $resultForAppend = treatmentKapsule($sheets[0],$sheets[1]);
        if (!empty($resultForAppend)) {
            foreach ($resultForAppend as $item) {
                wp_insert_post(wp_slash([
                    'post_title' => $item['name'],
                    'post_content' => $item['content'] ?? '',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'ping_status' => get_option('default_ping_status'),
                    'post_parent' => 0,
                ]));
            }
        }

        wp_delete_file($filePath);
    }
}

/**
 * @param $kapsuls
 * @param $products
 * @return array
 */
function treatmentKapsule($kapsuls, $products): array
{
    $kapsulsData = $resultData = [];

    foreach ($kapsuls['cells'] as $key => $kapsula) {
        if ($key === 0)
            continue;
        $kapsulsData[$kapsula[0]]['index'] = $kapsula[0];
        $kapsulsData[$kapsula[0]]['name'] = $kapsula[1];
        $kapsulsData[$kapsula[0]]['description'] = $kapsula[2];
    }
    if (!empty($kapsulsData)) {
        $resultData = appendProductInKapsuls($products, $kapsulsData);
    }
    return $resultData;
}

/**
 * @param $productsData
 * @param $kapsulsData
 * @return array
 */
function appendProductInKapsuls($productsData, $kapsulsData)
{
	$result = [];

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

				$productForData['url'] = $productData[0]->guid;
				$productForData['ID']  = $productData[0]->ID;
				$productForData['price']  = get_post_meta( $productData[0]->ID, '_price', 1 );
				$productForData['planned_date'] = get_post_meta( $productData[0]->ID, 'planned_date', 1 );
				$kapsulsData[ $product[0] ]['products_list'][ $productData[0]->ID ] = $productForData;
			}
		}

	}

	return $result;
}