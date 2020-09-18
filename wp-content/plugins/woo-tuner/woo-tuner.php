<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://volkov.co.il/
 * @since             0.1.0
 * @package           Woo_Tuner
 *
 * @wordpress-plugin
 * Plugin Name:       WooTuner
 * Plugin URI:        http://volkov.co.il/woo-tuner/
 * Description:       Customize your WooCommerce layouts with one click!
 * Version:           0.1.2
 * Author:            Alexander Volkov
 * Author URI:        http://volkov.co.il/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-tuner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-tuner-activator.php
 */
function activate_woo_tuner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-tuner-activator.php';
	Woo_Tuner_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-tuner-deactivator.php
 */
function deactivate_woo_tuner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-tuner-deactivator.php';
	Woo_Tuner_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_tuner' );
register_deactivation_hook( __FILE__, 'deactivate_woo_tuner' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-tuner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_woo_tuner() {

	$plugin = new Woo_Tuner();
	$plugin->run();

}
run_woo_tuner();
