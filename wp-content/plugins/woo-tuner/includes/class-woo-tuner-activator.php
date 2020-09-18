<?php

/**
 * Fired during plugin activation
 *
 * @link       http://volkov.co.il/
 * @since      1.0.0
 *
 * @package    Woo_Tuner
 * @subpackage Woo_Tuner/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Tuner
 * @subpackage Woo_Tuner/includes
 * @author     Alexander Volkov <vol4ikman@gmail.com>
 */
class Woo_Tuner_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		//Setup plugin version in options
		update_option('woo_tuner_version','1.0.0');
	}

}
