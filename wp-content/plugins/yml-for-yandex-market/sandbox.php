<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Sandbox function
 * 
 * @since	0.1.0
 *
 * @return	void
 */
function yfym_run_sandbox() {
	$x = false; // установите true, чтобы использовать песочницу
	if ( true === $x ) {
		printf( '%s<br/>',
			__( 'The sandbox is working. The result will appear below', 'yml-for-yandex-market' )
		);
		/* вставьте ваш код ниже */
		// Example:
		// $product = wc_get_product(8303);
		// echo $product->get_price();

		/* дальше не редактируем */
		printf( '<br/>%s',
			__( 'The sandbox is working correctly', 'yml-for-yandex-market' )
		);
	}
}