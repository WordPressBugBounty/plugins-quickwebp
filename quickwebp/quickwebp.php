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
 * Description:       Convert JPG and PNG images to WebP, compress uploads locally, improve image SEO, and unlock AVIF with QuickWebP Pro.
 * Version:           4.0.1
 * Author:            Webdeclic
 * Requires PHP: 	  8.1
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
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$is_local = isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1');

/**
 * If you are in local environment, you can use the version number as a timestamp for better cache management in your browser
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$version  = get_file_data( __FILE__, array( 'Version' => 'Version' ), false )['Version'];

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
 * Plugin File
 */
define( 'QUICKWEBP_PLUGIN_FILE', __FILE__ );

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
function quickwebp_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quickwebp-activator.php';
	Quickwebp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quickwebp-deactivator.php
 */
function quickwebp_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quickwebp-deactivator.php';
	Quickwebp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'quickwebp_activate' );
register_deactivation_hook( __FILE__, 'quickwebp_deactivate' );

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
function quickwebp_run() {

	if ( ! version_compare( PHP_VERSION, '7.4', '>=' ) ) {

		add_action( 'admin_notices', function() {
			?>
				<div class="notice notice-error">
					<p><?php esc_html_e( "Oops! QuickWebP isn't running because PHP is outdated. Update to PHP version 7.4", 'quickwebp' ); ?></p>
				</div>
			<?php
		});
		
	} else {

		$plugin = new Quickwebp();
		$plugin->run();
	}

}
quickwebp_run();
