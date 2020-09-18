<?php
/**
 * Customer approvation notification
 * @author  Woocommerce B2B
 * @version 1.0.0
 */
?>

<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php $user = get_userdata( $user_id ); ?>

<?php
	echo "= " . $email_heading . " =\n\n";

	echo esc_url( network_home_url( '/' ) ) . "\r\n\r\n";
	printf( __( 'Hi %s,', 'woocommerce-b2b' ), $user->first_name ) . "\r\n\r\n";
	echo __( 'Your account is approved and you are now abled to purchase.', 'woocommerce-b2b' ) . "\r\n";
	echo '<a href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '">' . __( 'Login and start purchasing', 'woocommerce-b2b' ) . '</a>' . "\r\n\r\n";

	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

	echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
?>