<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/includes
 * @author     Darko Lukic <lukic.pa@gmail.com>
 */
class Pressmates_Core_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		//Set default option on plugin initialization
		$set_default_options = new Pressmates_Core_Admin();
		$set_default_options->set_default_settings_on_plugin_activation();

        $register_portfolio_cpt = Pressmates_Core_Admin::create_cpt_portfolio();
	}

}
