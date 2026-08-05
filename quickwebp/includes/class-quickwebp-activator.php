<?php

/**
 * Fired during plugin activation
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 * @author     Webdeclic <contact@webdeclic.com>
 */
class Quickwebp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::maybe_add_rewrite_rules();
	}

	/**
	 * Add rewrite rules
	 */
	public static function maybe_add_rewrite_rules() {
		
		$web_mode	= get_option( 'quickwebp_settings_conversion_display_webp_mode', quickwebp_settings_default('quickwebp_settings_conversion_display_webp_mode') );
		if ( 'rewrite' !== $web_mode ) {
			return;
		}

		global $is_apache, $is_iis7, $is_nginx;

		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-apache.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-nginx.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-iis.php';

		if ( $is_apache ) {
			$rules = new Quickwebp_Apache();
		} elseif ( $is_iis7 ) {
			$rules = new Quickwebp_IIS();
		} elseif ( $is_nginx ) {
			$rules = new Quickwebp_Nginx();
		} else {
			return;
		}

		$rules->add();
	}
}
