<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 * @author     Webdeclic <contact@webdeclic.com>
 */
class Quickwebp_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		// load_plugin_textdomain has been discouraged since WordPress version 4.6.
		// https://make.wordpress.org/core/2016/07/06/i18n-improvements-in-4-6/
		
		// load_plugin_textdomain(
		// 	'quickwebp',
		// 	false,
		// 	dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		// );
	}
}
