<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'WC_Email_Status_Notification', false ) ) :

class WC_Email_Status_Notification extends WC_Email {

	public $user_id;
	public $recipients = array();

	public function __construct() {
		$this->id               = 'status_notification';
		$this->customer_email   = true;

		$this->title            = __( 'Status notification', 'woocommerce-b2b' );
		$this->description      = __( 'Send email notification to customer when his account is enabled.', 'woocommerce-b2b' );

		$this->template_html    = 'emails/status-notification.php';
		$this->template_plain   = 'emails/plain/status-notification.php';

		add_action( 'wcb2b_status_notification', array( $this, 'status_notification' ), 10, 2 );

		parent::__construct();

		$this->template_base = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/../../templates/';
	}

	public function status_notification( $user_id ) {
		$this->trigger( $user_id );
	}

	public function get_default_subject() {
		return __( 'Your account on {site_title} is approved!', 'woocommerce-b2b' );
	}

	public function get_default_heading() {
		return __( 'Your customer account is now abled to purchase.', 'woocommerce-b2b' );
	}

	public function trigger( $user_id ) {
		$user = get_userdata( $user_id );
		$this->recipient  = $user->user_email;

		// Skip if user hasn't a customer role
        if ( ! in_array( 'customer', $user->roles ) ) { return; }

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) { return; }
	    
	    $this->user_id = $user_id;

		$this->setup_locale();
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		$this->restore_locale();
	}

	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'user_id'		=> $this->user_id,
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this,
		), '', $this->template_base );
	}

	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'user_id'		=> $this->user_id,
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this,
		), '', $this->template_base );
	}
}

endif;

return new WC_Email_Status_Notification();