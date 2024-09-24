<?php

/**
 *
 * @link              https://webdeclic.com/
 * @since             1.0.0
 * @package           Quickwebp
 *
 * @wordpress-plugin
 * Plugin Name:       QuickWebP - Compress / Optimize Images & Convert WebP | SEO Friendly
 * Plugin URI:        https://webdeclic.com/projets/creation-de-lextension-wordpress-quickwebp/
 * Description:       QuickWebP is an image compression and optimization plugin for WordPress that automatically converts images to WebP when they are uploaded to the media library. It also optimizes the image to improve your site’s performance.
 * Version:           3.2.1
 * Author:            Webdeclic
 * Requires PHP: 	  7.4
 * Author URI:        https://webdeclic.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quickwebp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if your are in local or production environment
 */
$is_local = isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1');

/**
 * If you are in local environment, you can use the version number as a timestamp for better cache management in your browser
 */
$version  = $is_local ? time() : '3.2.1';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'QUICKWEBP_VERSION', $version );

/**
 * You can use this const for check if you are in local environment
 */
define( 'QUICKWEBP_DEV_MOD', $is_local );

/**
 * Plugin Name text domain for internationalization.
 */
define( 'QUICKWEBP_TEXT_DOMAIN', 'quickwebp' );

/**
 * Plugin Name Path for plugin includes.
 */
define( 'QUICKWEBP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Name URL for plugin sources (css, js, images etc...).
 */
define( 'QUICKWEBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quickwebp-activator.php
 */
function activate_quickwebp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quickwebp-activator.php';
	Quickwebp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quickwebp-deactivator.php
 */
function deactivate_quickwebp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quickwebp-deactivator.php';
	Quickwebp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_quickwebp' );
register_deactivation_hook( __FILE__, 'deactivate_quickwebp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-quickwebp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_quickwebp() {

	if ( ! version_compare( PHP_VERSION, '7.4', '>=' ) ) {

		add_action( 'admin_notices', function() {
			?>
				<div class="notice notice-error">
					<p><?php _e( "Oops! QuickWebP isn't running because PHP is outdated. Update to PHP version 7.4", QUICKWEBP_TEXT_DOMAIN ); ?></p>
				</div>
			<?php
		});
		
	} else {

		$plugin = new Quickwebp();
		$plugin->run();
	}

}
run_quickwebp();
