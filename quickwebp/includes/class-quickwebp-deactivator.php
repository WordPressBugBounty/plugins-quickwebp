<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 * @author     Webdeclic <contact@webdeclic.com>
 */
class Quickwebp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		self::remove_rewrite_rules();

	}

	/**
	 * Remove rewrite rules
	 */
	public static function remove_rewrite_rules() {
		global $is_apache, $is_iis7, $is_nginx;

		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-apache.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-nginx.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-iis.php';

		if ( $is_apache ) {
			$rules = new Apache();
		} elseif ( $is_iis7 ) {
			$rules = new IIS();
		} elseif ( $is_nginx ) {
			$rules = new Nginx();
		} else {
			return;
		}

		$rules->remove();
	}

}
