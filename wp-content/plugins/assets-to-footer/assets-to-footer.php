<?php

/**
 * Plugin Name: Assets to footer
 * Author: Sebastian Pisula
 * Author URI: mailto:sebastian.pisula@gmail.com
 * Version: 1.0.1
 * Description: Moves scripts and styles to the footer to decrease page load times. You can move all or specific styles and scripts to footer.
 * Text Domain: assets-to-footer
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class IC_Assets_To_Footer {
	/** @var string */
	private $html = '';

	/** @var array */
	private $assets = [];

	/** @var string */
	private $dir;

	/** @var string */
	private $file;

	/** @var string */
	private $basename;

	public function __construct( $file ) {
		$this->file     = $file;
		$this->dir      = dirname( $file );
		$this->basename = plugin_basename( $file );

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		add_action( 'admin_post_ic-move-assets', [ $this, 'move_assets' ] );
		add_action( 'admin_post_ic-reset-assets-settings', [ $this, 'reset_settings' ] );
		add_action( 'template_redirect', [ $this, 'init' ] );

		register_activation_hook( $file, [ $this, 'activation' ] );
	}

	public function plugins_loaded() {
		load_plugin_textdomain( 'assets-to-footer', false, dirname( $this->basename ) . '/languages' );
	}

	public function activation() {
		global $wp_version;

		__( 'Assets to footer', 'assets-to-footer' );
		__( 'Moves scripts and styles to the footer to decrease page load times. You can move all or specific styles and scripts to footer.', 'assets-to-footer' );

		//Check WordPress Version
		if ( version_compare( $wp_version, '4.1', '<' ) ) {
			deactivate_plugins( $this->basename );
			wp_die( __( 'This plugin requires WordPress in version at least 4.1', 'assets-to-footer' ) );
		}

		//Check PHP Version
		if ( version_compare( phpversion(), '5.4', '<' ) ) {
			deactivate_plugins( $this->basename );
			wp_die( __( 'This plugin requires PHP in version at least 5.4', 'assets-to-footer' ) );
		}
	}

	/**
	 * Save settings
	 */
	public function move_assets() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to do that.', 'assets-to-footer' ) );
		}

		check_admin_referer( 'ic-move-assets' );

		//Check type of asset
		if ( ! isset( $_GET['type'] ) || ! in_array( $_GET['type'], [ 'css', 'js' ] ) ) {
			wp_die( __( 'Invalid type', 'assets-to-footer' ) );
		}

		//Check current position of handle
		if ( ! isset( $_GET['position'] ) || ! in_array( $_GET['position'], [ 'header', 'footer' ] ) ) {
			wp_die( __( 'Invalid position', 'assets-to-footer' ) );
		}

		//Check exist handle
		if ( ! isset( $_GET['handle'] ) || empty( $_GET['handle'] ) ) {
			wp_die( __( 'Invalid handle', 'assets-to-footer' ) );
		}

		$options = $this->get_options();

		foreach ( explode( ',', $_GET['handle'] ) AS $handle ) {
			$options[ $_GET['type'] ][ $handle ]['all'] = $_GET['position'] === 'footer' ? 'header' : 'footer';
		}

		update_option( 'ic_assets_positions', $options, 1 );

		wp_redirect( wp_get_referer() );
		die();
	}

	/**
	 * Reset settings
	 */
	public function reset_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to do that.', 'assets-to-footer' ) );
		}

		check_admin_referer( 'ic-reset-assets-settings' );

		delete_option( 'ic_assets_positions' );

		wp_redirect( wp_get_referer() );
		die();
	}

	/**
	 * Register actions
	 */
	public function init() {
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu' ], 100 );
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		}

		add_action( 'wp_footer', [ $this, 'footer' ], 0 );

		add_filter( 'style_loader_tag', [ $this, 'script_loader_tag' ], 10000, 2 );
		add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10000, 2 );
	}

	public function wp_enqueue_scripts() {
		$file = 'assets/dist/css/style.css';
		wp_enqueue_style( 'ic-a2f', $this->get_plugin_url( $file ), [], $this->get_file_timestamp( $file ) );

		$file = 'assets/dist/js/app.min.js';
		wp_enqueue_script( 'ic-a2f', $this->get_plugin_url( $file ), [ 'jquery' ], $this->get_file_timestamp( $file ), 1 );
	}

	/**
	 * Filters the HTML script tag of an enqueued script.
	 *
	 * @param string $html The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 *
	 * @return string
	 */
	public function script_loader_tag( $html, $handle ) {
		//Build list
		$type = current_filter() === 'style_loader_tag' ? 'css' : 'js';

		$this->add_assets_list( $handle, $type );

		//Move to footer
		if ( $this->get_item_position( $type, $handle ) === 'footer' ) {
			$this->html .= $html;

			return '';
		}

		return $html;
	}

	/**
	 * Add scripts and styles to footer on top
	 */
	public function footer() {
		remove_filter( 'style_loader_tag', [ $this, 'script_loader_tag' ], 10000 );
		remove_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10000 );

		echo $this->html;
	}

	/**
	 * Build admin bar settings
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_menu( $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'ic-a2f',
				'title' => __( 'CSS &amp; Javascript', 'assets-to-footer' ),
			)
		);

		if ( isset( $this->assets['header'] ) ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'ic-a2f',
				'title'  => __( 'Header', 'assets-to-footer' ),
				'id'     => 'ic-a2f_header',
			) );
		}

		if ( isset( $this->assets['footer'] ) ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'ic-a2f',
				'title'  => __( 'Footer', 'assets-to-footer' ),
				'id'     => 'ic-a2f_footer',
			) );
		}

		foreach ( $this->assets AS $position => $types ) {
			foreach ( $types AS $type => $items ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'ic-a2f_' . $position,
					'title'  => strtoupper( $type ),
					'id'     => 'ic-a2f_' . $position . '_' . $type,
				) );

				if ( $position === 'footer' ) {
					$type_name             = __( 'Move all to header', 'assets-to-footer' );
					$type_name_alternative = __( 'Move selected to header', 'assets-to-footer' );
				} else {
					$type_name             = __( 'Move all to footer', 'assets-to-footer' );
					$type_name_alternative = __( 'Move selected to footer', 'assets-to-footer' );
				}

				$wp_admin_bar->add_menu( array(
					'parent' => 'ic-a2f_' . $position . '_' . $type,
					'title'  => esc_html( $type_name ),
					'id'     => 'ic-a2f_' . $position . '_' . $type . '_all_action',
					'href'   => wp_nonce_url( add_query_arg( [
						'handle'   => implode( ',', array_keys( $items ) ),
						'type'     => $type,
						'position' => $position,
						'action'   => 'ic-move-assets',
						'page'     => 'all',
					], admin_url( 'admin-post.php' ) ), 'ic-move-assets' ),
				) );

				$wp_admin_bar->add_menu( array(
					'parent' => 'ic-a2f_' . $position . '_' . $type,
					'title'  => esc_html( $type_name_alternative ),
					'id'     => 'ic-a2f_' . $position . '_' . $type . '_selected_action',
					'href'   => wp_nonce_url( add_query_arg( [
						'type'     => $type,
						'position' => $position,
						'action'   => 'ic-move-assets',
						'page'     => 'all',
					], admin_url( 'admin-post.php' ) ), 'ic-move-assets' ),
					'meta'   => [
						'class' => 'ic-hide-element js--move-assets'
					]
				) );

				foreach ( $items AS $handle => $details ) {
					$wp_admin_bar->add_menu( array(
						'parent' => 'ic-a2f_' . $position . '_' . $type,
						'title'  => '<label><input type="checkbox" class="ic-admin-bar-checkbox js--assets-checkbox" data-position="' . $position . '" data-type="' . $type . '" value="' . esc_attr( $handle ) . '"/> ' . esc_html( $handle ) . '</label>',
						'id'     => 'ic-a2f_' . $position . '_' . $type . '_' . $handle,
					) );

					if ( $position === 'footer' ) {
						$type_name = __( 'Move to header', 'assets-to-footer' );
					} else {
						$type_name = __( 'Move to footer', 'assets-to-footer' );
					}

					$wp_admin_bar->add_menu( array(
						'parent' => 'ic-a2f_' . $position . '_' . $type . '_' . $handle,
						'title'  => $type_name,
						'id'     => 'ic-a2f_' . $position . '_' . $type . '_' . $handle . '_action',
						'href'   => wp_nonce_url( add_query_arg( [
							'handle'   => $handle,
							'type'     => $type,
							'position' => $position,
							'action'   => 'ic-move-assets',
							'page'     => 'all',
						], admin_url( 'admin-post.php' ) ), 'ic-move-assets' ),
					) );
				}
			}
		}

		if ( $this->get_options() ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'ic-a2f',
				'title'  => __( 'Restore default', 'assets-to-footer' ),
				'id'     => 'ic-a2f_reset',
				'href'   => wp_nonce_url( add_query_arg( [
					'action' => 'ic-reset-assets-settings',
				], admin_url( 'admin-post.php' ) ), 'ic-reset-assets-settings' ),
			) );
		}
	}

	/**
	 * Build assets lists
	 *
	 * @param string $handle
	 * @param string $type
	 */
	private function add_assets_list( $handle, $type ) {
		if ( $handle === 'ic-a2f' ) {
			return;
		}

		if ( $type === 'js' && $this->is_footer_script( $handle ) ) {
			return;
		}

		$position = $this->get_item_position( $type, $handle );

		$this->assets[ $position ][ $type ][ $handle ] = [
			'position' => $position,
			'type'     => $type,
			'handle'   => $handle,
		];
	}

	/**
	 * @param string $type
	 * @param string $handle
	 *
	 * @return string
	 */
	private function get_item_position( $type, $handle ) {
		return $this->get_option( $type, $handle )['all'];
	}

	/**
	 * Check in footer
	 *
	 * @param string $handle
	 *
	 * @return bool
	 */
	private function is_footer_script( $handle ) {
		$wp_scripts = wp_scripts();

		return (bool) $wp_scripts->get_data( $handle, 'group' );
	}

	private function get_options() {
		return (array) get_option( 'ic_assets_positions', [] );
	}

	private function get_option( $type = '', $handle = '' ) {
		$options = $this->get_options();

		if ( empty( $type ) ) {
			return $options;
		}

		$options = isset( $options[ $type ] ) ? $options[ $type ] : [];

		if ( ! empty( $handle ) ) {
			if ( isset( $options[ $handle ] ) ) {
				return $options[ $handle ];
			} else {
				return [ 'all' => 'header' ];
			}
		}

		return $options;
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	private function get_plugin_url( $path = '' ) {
		return plugins_url( $path, $this->file );
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private function get_plugin_file_path( $file = '' ) {
		$file = ltrim( $file, '/' );

		return wp_normalize_path( $this->dir . '/' ) . $file;
	}

	/**
	 * @param string $file
	 *
	 * @return bool|int
	 */
	private function get_file_timestamp( $file ) {
		$file_path = $this->get_plugin_file_path( $file );

		if ( file_exists( $file_path ) ) {
			return filemtime( $file_path );
		}

		return false;
	}
}

new IC_Assets_To_Footer( __FILE__ );