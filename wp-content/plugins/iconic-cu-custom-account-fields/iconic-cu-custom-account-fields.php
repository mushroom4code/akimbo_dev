<?php
/*
Plugin Name: WooCommerce Custom Account Fields
Plugin URI: https://iconicwp.com/blog/the-ultimate-guide-to-adding-custom-woocommerce-user-account-fields/
Description: Add custom WooCommerce user account fields.
Author: Iconic
Version: 1.0.0
Author URI: https://iconicwp.com/products/
*/

/**
 * Get additional account fields.
 *
 * @return array
 */
function iconic_get_account_fields() {
	return apply_filters( 'iconic_account_fields', array(
		'INN'                 => array(
			'type'                 => 'text',
			'label'                => __( 'Инн', 'iconic' ),
			'hide_in_account'      => false,
			'hide_in_admin'        => false,
			'hide_in_checkout'     => false,
			'hide_in_registration' => false,
			'required'             => false,
		),
		'user_url'                   => array(
			'type'                 => 'text',
			'label'                => __( 'Сайт', 'iconic' ),
			'hide_in_account'      => false,
			'hide_in_admin'        => false,
			'hide_in_checkout'     => false,
			'hide_in_registration' => false,
			'required'             => false,
		),

		'iconic-register-select'     => array(
			'type'    => 'select',
			'label'   => __( 'Сфера продаж', 'iconic' ),
			'options' => array(
				'' => __( 'Выберите один из вариантов...', 'iconic' ),
				1  => __( 'Магазин', 'iconic' ),
				2  => __( 'Сеть', 'iconic' ),
				3  => __( 'Опт', 'iconic' ),
                4  => __( 'Крупный опт', 'iconic' ),
			),
		),
        'all_name'                   => array(
            'type'                 => 'text',
            'label'                => __( 'Отчество', 'iconic' ),
            'hide_in_account'      => false,
            'hide_in_admin'        => false,
            'hide_in_checkout'     => false,
            'hide_in_registration' => true,
            'required'             => false,
        ),

	) );
}

/**
 * Add post values to account fields if set.
 *
 * @param array $fields
 *
 * @return array
 */
function iconic_add_post_data_to_account_fields( $fields ) {
	if ( empty( $_POST ) ) {
		return $fields;
	}

	foreach ( $fields as $key => $field_args ) {
		if ( empty( $_POST[ $key ] ) ) {
			$fields[ $key ]['value'] = '';
			continue;
		}

		$fields[ $key ]['value'] = $_POST[ $key ];
	}

	return $fields;
}

add_filter( 'iconic_account_fields', 'iconic_add_post_data_to_account_fields', 10, 1 );

/**
 * Add fields to registration form and account area.
 */
function iconic_print_user_frontend_fields() {
	$fields            = iconic_get_account_fields();
	$is_user_logged_in = is_user_logged_in();

	foreach ( $fields as $key => $field_args ) {
		$value = null;

		if ( ! iconic_is_field_visible( $field_args ) ) {
			continue;
		}

		if ( $is_user_logged_in ) {
			$user_id = iconic_get_edit_user_id();
			$value   = iconic_get_userdata( $user_id, $key );
		}

		$value = isset( $field_args['value'] ) ? $field_args['value'] : $value;

		woocommerce_form_field( $key, $field_args, $value );
	}
}

add_action( 'woocommerce_register_form', 'iconic_print_user_frontend_fields', 10 ); // register form
add_action( 'woocommerce_edit_account_form', 'iconic_print_user_frontend_fields', 10 ); // my account

/**
 * Get user data.
 *
 * @param $user_id
 * @param $key
 *
 * @return mixed|string
 */
function iconic_get_userdata( $user_id, $key ) {
	if ( ! iconic_is_userdata( $key ) ) {
		return get_user_meta( $user_id, $key, true );
	}

	$userdata = get_userdata( $user_id );

	if ( ! $userdata || ! isset( $userdata->{$key} ) ) {
		return '';
	}

	return $userdata->{$key};
}

/**
 * Modify checkboxes/radio fields.
 *
 * @param $field
 * @param $key
 * @param $args
 * @param $value
 *
 * @return string
 */
function iconic_form_field_modify( $field, $key, $args, $value ) {
	ob_start();
	iconic_print_list_field( $key, $args, $value );
	$field = ob_get_clean();

	if ( $args['return'] ) {
		return $field;
	} else {
		echo $field;
	}
}

add_filter( 'woocommerce_form_field_checkboxes', 'iconic_form_field_modify', 10, 4 );
add_filter( 'woocommerce_form_field_radio', 'iconic_form_field_modify', 10, 4 );

/**
 * Get currently editing user ID (frontend account/edit profile/edit other user).
 *
 * @return int
 */
function iconic_get_edit_user_id() {
	return isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : get_current_user_id();
}


/**
 * Save registration fields.
 *
 * @param int $customer_id
 */
function iconic_save_account_fields( $customer_id ) {
	$fields         = iconic_get_account_fields();
	$sanitized_data = array();

	foreach ( $fields as $key => $field_args ) {
		if ( ! iconic_is_field_visible( $field_args ) ) {
			continue;
		}

		$sanitize = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : 'wc_clean';
		$value    = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';

		if ( iconic_is_userdata( $key ) ) {
			$sanitized_data[ $key ] = $value;
			continue;
		}

		update_user_meta( $customer_id, $key, $value );
	}

	if ( ! empty( $sanitized_data ) ) {
		$sanitized_data['ID'] = $customer_id;
		wp_update_user( $sanitized_data );
	}
}

add_action( 'woocommerce_created_customer', 'iconic_save_account_fields' ); // register/checkout
add_action( 'personal_options_update', 'iconic_save_account_fields' ); // edit own account admin
add_action( 'edit_user_profile_update', 'iconic_save_account_fields' ); // edit other account
add_action( 'woocommerce_save_account_details', 'iconic_save_account_fields' ); // edit WC account

/**
 * Is this field core user data.
 *
 * @param $key
 *
 * @return bool
 */
function iconic_is_userdata( $key ) {
	$userdata = array(
		'user_pass',
		'user_login',
        'all_name',
		'user_nicename',
        'address_new',
        'user_url',
		'display_name',
		'nickname',
		'INN',
		'Sfera_prod',
		'description',
		'rich_editing',
		'user_registered',
		'role',
		'jabber',
		'aim',
		'yim',
		'show_admin_bar_front',
	);

	return in_array( $key, $userdata );
}

/**
 * Is field visible.
 *
 * @param $field_args
 *
 * @return bool
 */
function iconic_is_field_visible( $field_args ) {
	$visible = true;
	$action  = filter_input( INPUT_POST, 'action' );

	if ( is_admin() && ! empty( $field_args['hide_in_admin'] ) ) {
		$visible = false;
	} elseif ( ( is_account_page() || $action === 'save_account_details' ) && is_user_logged_in() && ! empty( $field_args['hide_in_account'] ) ) {
		$visible = false;
	} elseif ( ( is_account_page() || $action === 'save_account_details' ) && ! is_user_logged_in() && ! empty( $field_args['hide_in_registration'] ) ) {
		$visible = false;
	} elseif ( is_checkout() && ! empty( $field_args['hide_in_checkout'] ) ) {
		$visible = false;
	}

	return $visible;
}

/**
 * Add fields to admin area.
 */
function iconic_print_user_admin_fields() {
	$fields = iconic_get_account_fields();
	?>
	<h2><?php _e( 'Дополнительная информация', 'iconic' ); ?></h2>
	<table class="form-table" id="iconic-additional-information">
		<tbody>
		<?php foreach ( $fields as $key => $field_args ) { ?>
			<?php
			if ( ! iconic_is_field_visible( $field_args ) ) {
				continue;
			}

			$user_id = iconic_get_edit_user_id();
			$value   = iconic_get_userdata( $user_id, $key );
			?>
			<tr>
				<th>
					<label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
				</th>
				<td>
					<?php $field_args['label'] = false; ?>
					<?php woocommerce_form_field( $key, $field_args, $value ); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
}

add_action( 'show_user_profile', 'iconic_print_user_admin_fields', 30 ); // admin: edit profile
add_action( 'edit_user_profile', 'iconic_print_user_admin_fields', 30 ); // admin: edit other users

/**
 * Validate fields on frontend.
 *
 * @param WP_Error $errors
 *
 * @return WP_Error
 */
function iconic_validate_user_frontend_fields( $errors ) {
	$fields = iconic_get_account_fields();

	foreach ( $fields as $key => $field_args ) {
		if ( empty( $field_args['required'] ) ) {
			continue;
		}

		if ( ! isset( $_POST['register'] ) && ! empty( $field_args['hide_in_account'] ) ) {
			continue;
		}

		if ( isset( $_POST['register'] ) && ! empty( $field_args['hide_in_registration'] ) ) {
			continue;
		}

		if ( empty( $_POST[ $key ] ) ) {
			$message = sprintf( __( '%s is a required field.', 'iconic' ), '<strong>' . $field_args['label'] . '</strong>' );
			$errors->add( $key, $message );
		}
	}

	return $errors;
}

add_filter( 'woocommerce_registration_errors', 'iconic_validate_user_frontend_fields', 10 );
add_filter( 'woocommerce_save_account_details_errors', 'iconic_validate_user_frontend_fields', 10 );

/**
 * Show fields at checkout.
 */
function iconic_checkout_fields( $checkout_fields ) {
	$fields = iconic_get_account_fields();

	foreach ( $fields as $key => $field_args ) {
		if ( ! iconic_is_field_visible( $field_args ) ) {
			continue;
		}

		// Make sure our fields have a default priority so
		// no error is thrown when sorting them.
		$field_args['priority'] = isset( $field_args['priority'] ) ? $field_args['priority'] : 0;

		$checkout_fields['account'][ $key ] = $field_args;
	}

	// Default password field has no priority which throws an
	// error when it tries to order the fields by priority.
	if ( ! empty( $checkout_fields['account']['account_password'] ) && ! isset( $checkout_fields['account']['account_password']['priority'] ) ) {
		$checkout_fields['account']['account_password']['priority'] = 0;
	}

	return $checkout_fields;
}

add_filter( 'woocommerce_checkout_fields', 'iconic_checkout_fields', 10, 1 );