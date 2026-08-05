<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The class responsible for handling the migration.
 *
 * @link       https://webdeclic.com
 * @since      3.4.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Migration {

	/**
	 * Init the migration.
	 */
	public function init_migration() {

		$last_version = get_option( 'quickwebp_plugin_version', '1.0.0' );

		if ( version_compare( $last_version, QUICKWEBP_VERSION, '<' ) ) {
			$this->run_migration_tasks( $last_version );
			update_option( 'quickwebp_plugin_version', QUICKWEBP_VERSION );
		}
	}

	/**
	 * Run migration tasks based on the last version.
	 */
	private function run_migration_tasks( $last_version ) {

		if ( version_compare( $last_version, '3.4.0', '<' ) ) {

			$quality = get_option( 'quickwebp_settings_conversion_quality', '' );
			if ( ! empty( $quality ) && ! in_array( $quality, array( 'low', 'medium', 'high', 'extra_high' ) ) ) {

				$quality = (int) $quality;

				if ( $quality < 50 ) {
					$quality = 'low';
				} elseif ( $quality < 60 ) {
					$quality = 'medium';
				} elseif ( $quality < 80 ) {
					$quality = 'high';
				} else {
					$quality = 'extra_high';
				}

				update_option( 'quickwebp_settings_conversion_quality', $quality );
			}
		}
	}
}
