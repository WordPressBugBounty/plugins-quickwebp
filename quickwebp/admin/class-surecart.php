<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The class responsible for handling the surecart.
 *
 * @link       https://webdeclic.com
 * @since      3.4.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Surecart {

	/**
	 * The client instance.
	 */
	private $client;

	/**
	 * The settings instance.
	 */
	private $settings;

	/**
	 * The updater instance.
	 */
	private $updater;

	/**
	 * The transient id.
	 */
	private $transient_id = 'quickwebp_surecart_license';

	/**
	 * Init the surecart.
	 */
	public function init_surecart() {
		global $quickwebp_surecart_client;

		if ( ! class_exists( 'SureCartQuickWebP\Licensing\Client' ) ) {
			require_once QUICKWEBP_PLUGIN_PATH . 'licensing/src/Client.php';
		}

		$this->client              = new \SureCartQuickWebP\Licensing\Client( 'QuickWebP', 'pt_KNa5RLetAzsYTHKMLAf9Gtey', QUICKWEBP_PLUGIN_FILE );
		$quickwebp_surecart_client = $this->client;

		$this->settings = $this->client->settings();
		$this->updater  = $this->client->updater();

		$this->client->settings()->add_page( array(
			'type'        => 'submenu',
			'parent_slug' => 'upload.php',
			'page_title'  => esc_html__( 'QuickWebP License', 'quickwebp' ),
			'menu_title'  => esc_html__( 'QuickWebP License', 'quickwebp' ),
			'capability'  => 'manage_options',
			'menu_slug'   => $this->client->slug . '-manage-license',
			'icon_url'    => '',
			'position'    => null,
		));
	}

	/**
	 * Enqueue scripts and styles
	 * 
	 * @since 1.15.0
	 */
	public function enqueue_scripts_styles() {
		global $quickwebp_surecart_client;

		$is_pro = false;
		if ( $quickwebp_surecart_client ) {
			$is_pro = $quickwebp_surecart_client->license()->is_valid();
		}

		$surecart_license_assets = include( QUICKWEBP_PLUGIN_PATH . 'public/assets/build/surecart-license.asset.php' );
		wp_enqueue_style( 'quickwebp_surecart_license', QUICKWEBP_PLUGIN_URL . 'public/assets/build/surecart-license.css', array(), $surecart_license_assets['version'], 'all' );
		wp_enqueue_script( 'quickwebp_surecart_license', QUICKWEBP_PLUGIN_URL . 'public/assets/build/surecart-license.js', $surecart_license_assets['dependencies'], $surecart_license_assets['version'], true );
		wp_localize_script( 'quickwebp_surecart_license', 'quickwebp_surecart_license', array(
			'activated' => $is_pro,
			'i18n'      => array(
				'upgrade' => esc_js( '★ ' . esc_html( __( 'Upgrade Pro', 'quickwebp' ) ) ),
			),
		));
	}

	/**
	 * License activated
	 * 
	 * @since 1.15.0
	 */
	public function license_activated() {
		$activation = get_transient( $this->transient_id );

		if ( false === $activation ) {
			$activation = $this->settings->get_activation();
			if ( ! empty( $activation->id ) ) {
				set_transient( $this->transient_id, true, HOUR_IN_SECONDS * 2 );
			}
		}

		return $activation;
	}
}
