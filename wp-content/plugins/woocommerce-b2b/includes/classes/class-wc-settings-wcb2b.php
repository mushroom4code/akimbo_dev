<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'WC_Settings_WCB2B', false ) ) :

/* WC_Settings_B2B */
class WC_Settings_WCB2B extends WC_Settings_Page {

	/* Constructor */
	public function __construct() {
		$this->id    = 'woocommerce-b2b';
		$this->label = __( 'B2B', 'woocommerce-b2b' );
		parent::__construct();
	}

	/* Get sections */
	public function get_sections() {
		$sections[''] = __( 'Settings', 'woocommerce-b2b' );
		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/* Output the settings */
	public function output() {
		global $current_section;
		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );
	}

	/* Save settings */
	public function save() {
		global $current_section;
		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}

	/* Get settings array */
	public function get_settings( $current_section = '' ) {
		$settings = apply_filters( 'wcb2b_general_settings', array(
			'section_title-wcb2b_enable' => array(
	            'name'     => __( 'B2B mode', 'woocommerce-b2b' ),
	            'type'     => 'title',
	            'desc'     => __( 'By enabling B2B mode only registered customers will be able to view product prices and have the opportunity to make purchases. Also enable new products options to force customers to buy minimum stock and to buy by pack', 'woocommerce-b2b' )
	        ),
	        'option-wcb2b_enable' => array(
	            'name' => __( 'Enable', 'woocommerce-b2b' ),
	            'type' => 'checkbox',
	            'desc' => __( 'Turn on Busines to Business mode for your shop', 'woocommerce-b2b' ),
	            'id'   => 'wcb2b_enable'
	        ),
	        'section_end-wcb2b_enable' => array(
	             'type' => 'sectionend'
	        )
		) );

		// Add plugin depending options
    	if ( get_option( 'wcb2b_enable' ) === 'yes' ) {
	    	$settings = $settings + array(
	    		/* SHOP OPTIONS */
	            'wcb2b-section_title-shop_options' => array(
	                'name'          	=> __( 'Shop options', 'woocommerce-b2b' ),
	                'type'          	=> 'title',
	                'desc'          	=> __( 'Customize shop fields to improve B2B experience: choose to add indispensable fields.', 'woocommerce-b2b' )
	            ),

	            // Show prices only to logged in customers 
	            'wcb2b-option-hide_prices' => array(
	                'name'          	=> __( 'Hide prices', 'woocommerce-b2b' ),
	                'type'          	=> 'checkbox',
	                'desc'          	=> __( 'Hide prices for not logged in customers', 'woocommerce-b2b' ),
	                'desc_tip'      	=> __( 'Price is replaced by a message linked to login page', 'woocommerce-b2b' ),
	                'id'            	=> 'wcb2b_hide_prices'
	            ),

	            // Add VAT number field in checkout
	            'wcb2b-option-add_vatnumber' => array(
	                'name'          	=> __( 'VAT number', 'woocommerce-b2b' ),
	                'type'          	=> 'checkbox',
	                'desc'          	=> __( 'Add VAT number field to billing address', 'woocommerce-b2b' ),
	                'desc_tip'      	=> __( 'VAT number field is added to checkout form, order emails, order details', 'woocommerce-b2b' ),
	                'id'            	=> 'wcb2b_add_vatnumber'
	            ),
	            'wcb2b-section_end-shop_options' => array(
	                 'type'         	=> 'sectionend'
	            ),

	            /* PURCHASE OPTIONS */
	            'wcb2b-section_title-purchase_options' => array(
	                'name'          	=> __( 'Purchase options', 'woocommerce-b2b' ),
	                'type'          	=> 'title',
	                'desc'          	=> __( 'Customers can place an order only reaching the minimum amount. If the minimum amount isn\'t reached, display a message in checkout in place of payment methods, but you can display an alert also in shopping cart in place of "Proceed to checkout" button.', 'woocommerce-b2b' )
	            ),

	            // Minimum amount
	            'wcb2b-option-min_purchase_amount' => array(
	                'name'          	=> __( 'Min purchase amount', 'woocommerce-b2b' ),
	                'type'          	=> 'number',
	                'desc'          	=> __( 'To disable the option, set 0', 'woocommerce-b2b' ),
	                'id'            	=> 'wcb2b_min_purchase_amount',
	                'class'				=> 'small-text'
	            ),

	            // Display message in cart
	            'wcb2b-option-display_min_purchase_cart_message' => array(
	                'name'          	=> __( 'Display message in cart', 'woocommerce-b2b' ),
	                'type'          	=> 'checkbox',
	                'desc'          	=> __( 'Display a message in the shopping cart page to alert that minimum amount isn\'t reached yet', 'woocommerce-b2b' ),
	                'id'            	=> 'wcb2b_display_min_purchase_cart_message',
	                'show_if_checked' 	=> 'option',
	                'checkboxgroup' 	=> 'start'
	            ),

	            // Prevent checkout button
	            'wcb2b-option-prevent_checkout_button' => array(
	                'name'          	=> __( 'Prevent proceed to checkout', 'woocommerce-b2b' ),
	                'type'          	=> 'checkbox',
	                'desc'          	=> __( 'Remove "Proceed to checkout" button in shopping cart if customer doesn\'t reach the minimum amount', 'woocommerce-b2b' ),
	                'id'            	=> 'wcb2b_prevent_checkout_button',
	                'show_if_checked' 	=> 'yes', 
	                'checkboxgroup' 	=> 'end'
	            ),
	            'wcb2b-section_end-purchase_options' => array(
	                 'type'         	=> 'sectionend'
	            ),

	            /* CUSTOMER OPTIONS */
	            'wcb2b-section_title-customer_options' => array(
	                'name'          	=> __( 'Customer options', 'woocommerce-b2b' ),
	                'type'          	=> 'title',
	                'desc'          	=> __( 'Choose the users to approve and decide which informations you want to display in customer account.', 'woocommerce-b2b' )
	            ),

	            // Moderate registration
	            'wcb2b-option-moderate_customer_registration' => array(
	                'name' 				=> __( 'Moderate registration', 'woocommerce-b2b' ),
	                'type' 				=> 'checkbox',
	                'desc' 				=> __( 'Customers can login only after admin approvation', 'woocommerce-b2b' ),
	                'desc_tip'      	=> __( 'Add new columns to users list and new field to profile page in backend to manage customer status', 'woocommerce-b2b' ),
	                'id'   				=> 'wcb2b_moderate_customer_registration'
	            ),

	            // Extend registration form
	            'wcb2b-option-extend_registration_form' => array(
	                'name' 				=> __( 'Registration form', 'woocommerce-b2b' ),
	                'type' 				=> 'checkbox',
	                'desc' 				=> __( 'Add billing data to registration form', 'woocommerce-b2b' ),
	                'desc_tip'      	=> __( 'Extend registration form with billing fields. If checked, enable by default the option \'Enable customer registration on the "My account" page\' in Accounts tab under WooCommerce settings', 'woocommerce-b2b' ),
	                'id'   				=> 'wcb2b_extend_registration_form'
	            ),
	            // Registration notice
	            'wcb2b-option-registration_notice' => array(
	                'name' 				=> __( 'Registration notice', 'woocommerce-b2b' ),
	                'type' 				=> 'checkbox',
	                'desc' 				=> __( 'Send email to admin on every new customer account registration', 'woocommerce-b2b' ),
	                'desc_tip'      	=> __( 'When a customer create an account, an email is send to admin to inform him', 'woocommerce-b2b' ),
	                'id'   				=> 'wcb2b_registration_notice'
	            ),
	            'wcb2b-section_end-customer_options' => array(
	                 'type'         	=> 'sectionend'
	            ),

	            /* GROUP OPTIONS */
	            'wcb2b-section_title-groups' => array(
	                'name'          	=> __( 'Customer groups', 'woocommerce-b2b' ),
	                'type'          	=> 'title',
	                'desc'          	=> __( 'Create your favourite groups and assign to your customers to give a discount on their purchases.', 'woocommerce-b2b' )
	            ),

	            // Display percentage in user area
	            'wcb2b-option-show_customer_discount' => array(
	                'name' 				=> __( 'Show discount', 'woocommerce-b2b' ),
	                'type' 				=> 'checkbox',
	                'desc' 				=> __( 'Customers can view discount amount assigned to them in their own account area', 'woocommerce-b2b' ),
	                'id'   				=> 'wcb2b_show_customer_discount',
	                'checkboxgroup' 	=> 'start'
	            ),

	            // Display percentage in product page
	            'wcb2b-option-show_customer_discount_product' => array(
	                'type' 				=> 'checkbox',
	                'desc' 				=> __( 'Customers can view discount amount assigned to them in single product page', 'woocommerce-b2b' ),
	                'id'   				=> 'wcb2b_show_customer_discount_product',
	                'checkboxgroup' 	=> 'end'
	            ),

	            // Enable product categories by group
	            'wcb2b-option-product_cat_visibility' => array(
	                'name' 				=> __( 'Product categories visibility', 'woocommerce-b2b' ),
	                'type' 				=> 'checkbox',
	                'id'   				=> 'wcb2b_product_cat_visibility',
	                'desc' 				=> __( 'Display product categories by group', 'woocommerce-b2b' ),
	                'desc_tip'			=> __( 'When enabled, every product category can be set to be visible only to certain groups', 'woocommerce-b2b' )
	            ),
	            
	            // Redirect on hidden product categories
	            'wcb2b-option-hidden_redirect_page' => array(
	                'name' 				=> __( 'Redirect on page', 'woocommerce-b2b' ),
	                'type' 				=> 'select',
	                'class'    			=> 'wc-enhanced-select',
	                'id'   				=> 'wcb2b_redirect_not_allowed',
	                'desc' 				=> '<br />' . __( 'When users go directly to a not visible product category or a not visible product', 'woocommerce-b2b' ),
	                'options'			=> array_reduce( get_pages(), function( $result, $item ) {
					    $item = (array)$item;
					    $result[$item['ID']] = $item['post_title'];
					    return $result;
					}, array(
						'null' 	=> __( '-- Do nothing --', 'woocommerce-b2b' ),
	                	'0'  	=> __( '404 (not found) page', 'woocommerce-b2b' )
					) )
	            ),
	            'wcb2b-section_end-groups' => array(
	                 'type'         	=> 'sectionend'
	            )
        	);
			
	    }
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}
}

endif;

return new WC_Settings_WCB2B();
