<?php

class NotisendOptionsPage {

	private ?array $_options;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'notisend_add_options_page' ] );
		add_action( 'admin_init', [ $this, 'notisend_register_options' ] );
	}

	public function notisend_add_options_page() {
		add_options_page(
			'Настройка notisend',
			'Notisend',
			'manage_options',
			'noticend-options-page',
			[ $this, 'noticend_options_page' ],
		);
	}

	public function noticend_options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$options = get_option('notisend_options');
        $this->_options = empty($options) ? [] : $options;

		?>
        <div class="wrap">
            <form method="post" action="options.php">
				<?php
				settings_fields( 'notisend-option-group' );
				do_settings_sections( 'notisend_options_page' );

                ?>

                <?php
				submit_button();
				?>
            </form>
            <div style="display: inline-block">
                <span id="notisend_import_customer_spinner"  class="spinner"></span>
                <a id="notisend_import_customer" href="#" class="button button-primary">
                    Импортировать клиентов
                </a>
            </div>
        </div>

		<?php
	}

	public function notisend_register_options() {
		register_setting(
			'notisend-option-group',
			'notisend_options',
			[ $this, 'notisend_save_options' ]
		);

		add_settings_section(
			'notisend-options',
			'Настройки notisend',
			'',
			'notisend_options_page'
		);

		add_settings_field(
			'wp-notisend-enable',
			'Включен',
			[ $this, 'wp_notisend_enable_callback' ],
			'notisend_options_page',
			'notisend-options'
		);

		add_settings_field(
			'wp-notisend-api-token',
			'API TOKEN',
			[ $this, 'wp_notisend_api_token_callback' ],
			'notisend_options_page',
			'notisend-options'
		);

		add_settings_field(
			'wp-notisend-group_client',
			'Ид группы клиентов',
			[ $this, 'wp_notisend_group_client_callback' ],
			'notisend_options_page',
			'notisend-options'
		);
	}
	public function wp_notisend_enable_callback() {
		?>
        <input type="checkbox" id="notisend_enable" name="notisend_options[enable]"
            <?= $this->_options['enable'] ? 'checked' : '' ?> />
		<?php
	}

	public function wp_notisend_api_token_callback() {
		?>
        <input type="text" size="35" id="notisend_enable" name="notisend_options[api_token]"
			value="<?= $this->_options['api_token'] ?? '' ?>" />
		<?php
	}

	public function wp_notisend_group_client_callback() {
		?>
        <input type="text" size="10" id="notisend_enable" name="notisend_options[group_client]"
			value="<?= $this->_options['group_client'] ?? '' ?>" />
		<?php
	}

	public function notisend_save_options($value) {
		return $value;
	}

}

if ( is_admin() ) {
	new NotisendOptionsPage();
}

add_action('admin_enqueue_scripts', 'notisend_add_script');
function notisend_add_script($handle) {
    if ($handle==='settings_page_noticend-options-page') {
	    wp_enqueue_script( 'notissend_admin_script', get_template_directory_uri() . '/includes/notisend/js/notisend-admin.js' );
    }
}


