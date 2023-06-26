<?php

use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore;

if ( class_exists( "WP_CLI" ) ) {
	class ServiceFunctionCLI {

		/**
		 * Generate customer records for all verified users
		 *
		 */
		public function update_customer() {
			update_customer();
			WP_CLI::line( 'Success update customer list' );
		}
	}
}

function update_customer() {
	$args      = [
		'meta_query' => [
			'relation' => 'OR',
			[
				'key'     => 'alg_wc_ev_is_activated',
				'value'   => '0',
				'compare' => '!='
			],
			[
				'key'     => 'alg_wc_ev_is_activated',
				'compare' => 'NOT EXISTS'
			]
		],
		'orderby'    => 'id',
		'number'     => 0,
	];
	$listUsers = get_users( $args );
	foreach ( $listUsers as $user ) {
		DataStore::update_registered_customer( $user->id );
	}
}

function service_function_register_commands() {
	WP_CLI::add_command( 'service_function', 'ServiceFunctionCLI' );
}

add_action( 'cli_init', 'service_function_register_commands' );