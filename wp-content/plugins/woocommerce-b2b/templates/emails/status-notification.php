<?php
/**
 * Customer approvation notification
 * @author  Woocommerce B2B
 * @version 1.0.0
 */
?>

<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php $user = get_userdata( $user_id ); ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( 'Hi %s,', 'woocommerce-b2b' ), $user->first_name ); ?></p>
<p>
	<?php _e( 'Your account is approved and you are now abled to purchase.', 'woocommerce-b2b' ); ?>
	<br />
	<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>"><?php _e( 'Login and start purchasing', 'woocommerce-b2b' ); ?></a>
</p>
<p></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>