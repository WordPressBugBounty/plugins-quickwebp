<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Settings {

	const QUICKWEBP_GET_LICENSE_URL    = 'https://solutions.leyoweb.com/products/quickwebp/';
	const QUICKWEBP_GET_MY_ACCOUNT_URL = 'https://solutions.leyoweb.com/customer-dashboard/';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Enqueue scripts and styles
	 * 
	 */
	public function enqueue_scripts_styles( $hook_suffix ) {
		if ( $hook_suffix == 'toplevel_page_quickwebp-settings' || $hook_suffix == 'media_page_quickwebp-settings' ) {

			$admin_main_settings_assets = include( QUICKWEBP_PLUGIN_PATH . 'public/assets/build/admin-main-settings.asset.php' );
			wp_enqueue_style( 'quickwebpoi_admin_main_settings', QUICKWEBP_PLUGIN_URL . 'public/assets/build/admin-main-settings.css', array(), $admin_main_settings_assets['version'], 'all' );
			wp_enqueue_script( 'quickwebpoi_admin_main_settings', QUICKWEBP_PLUGIN_URL . 'public/assets/build/admin-main-settings.js', $admin_main_settings_assets['dependencies'], $admin_main_settings_assets['version'], true );
			wp_localize_script( 'quickwebpoi_admin_main_settings', 'QUICKWEBP_ADMIN_SETTINGS', array(
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'image_optimize_nonce' ),
				'preview_image_data' => $this->get_preview_image_data(),
				'default_image_url'  => QUICKWEBP_PLUGIN_URL . 'public/assets/img/preview.jpg',
			));
		}
	}
		
	/**
	 * add_settings_menu
	 *
	 * @return void
	 */
	public function add_settings_menu() {
		add_submenu_page(
			'upload.php',
			__('QuickWebP Settings', 'quickwebp'),
			__('QuickWebP', 'quickwebp'),
			'manage_options',
			'quickwebp-settings',
			array( $this, 'render_settings_page' )
		);
	}
	
	/**
	 * render_settings_page
	 *
	 * @return void
	 */
	public function render_settings_page() {
		global $is_nginx, $quickwebp_surecart_client;

		$php_version_valid = version_compare( PHP_VERSION, '8.1', '>=' );
		$license_valid     = false;
		if ( $quickwebp_surecart_client ) {
			$license_valid = $quickwebp_surecart_client->license()->is_valid();
		}
		
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['quickwebp_settings_conversion'] ) ) {
			update_option( 'quickwebp_settings_onboarding_completed', '1' );
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$wpmtk_is_active       = in_array( 'wpmastertoolkit/wp-mastertoolkit.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		$show_onboarding       = '1' !== (string) get_option( 'quickwebp_settings_onboarding_completed', '0' );
		$conversion            = get_option( 'quickwebp_settings_conversion', quickwebp_settings_default( 'quickwebp_settings_conversion' ) );
		$conversion_enabled    = '1' === (string) $conversion;
		$save_original         = get_option( 'quickwebp_settings_conversion_save_original', quickwebp_settings_default( 'quickwebp_settings_conversion_save_original' ) );
		$save_original         = is_array( $save_original ) ? $save_original : array();
		$save_original_enabled = in_array( 'checked', $save_original, true );
		$display_mode          = get_option( 'quickwebp_settings_conversion_display_webp_mode', quickwebp_settings_default( 'quickwebp_settings_conversion_display_webp_mode' ) );
		$conversion_quality    = get_option( 'quickwebp_settings_conversion_quality', quickwebp_settings_default( 'quickwebp_settings_conversion_quality' ) );

		$profile_label = __( 'Custom profile', 'quickwebp' );
		if ( $conversion_enabled && ! $save_original_enabled ) {
			$profile_label = __( 'New site profile', 'quickwebp' );
		}
		if ( $conversion_enabled && $save_original_enabled ) {
			$profile_label = __( 'Existing site profile', 'quickwebp' );
		}

		$preview_image_data      = $this->get_preview_image_data();
		$preview_image_data_webp = $preview_image_data['1_' . $conversion_quality] ?? $preview_image_data['1_medium'];
		$preview_image_data_avif = $preview_image_data['2_' . $conversion_quality] ?? $preview_image_data['2_medium'];

		require_once QUICKWEBP_PLUGIN_PATH . 'admin/templates/page-settings.php';
	}
	
	/**
	 * render_component
	 *
	 * @param  mixed $data
	 * @return void
	 */
	public function render_component( $data = array() ) {
		$data['type'] 		= $data['type'] ?? 'text';
		$data['name'] 		= $data['name'] ?? '';
		$file_name 			= $data['type'] == 'text' || $data['type'] == 'email' || $data['type'] == 'tel' || $data['type'] == 'number' ? 'text' : $data['type'];
		$path_to_component 	= QUICKWEBP_PLUGIN_PATH . 'admin/components/' . $file_name . '.php';

		if( file_exists( $path_to_component ) ) {
			?>
			<tr>
				<th>
					<label for="<?php echo esc_attr( $data['name'] ?? '' ); ?>"><?php echo esc_html( $data['label'] ?? '' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $data['name'] ?? '' ); ?>-container">
					<?php include $path_to_component; ?>
					<?php if( isset( $data['description'] ) ) { 
						?>
						<p class="description"><?php echo esc_html( $data['description'] ); ?></p>
						<?php 
					} 
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Add the settings link
	 */
	public function add_settings_link( $plugin_actions, $plugin_file ) {

		$new_actions = array();

		if ( 'quickwebp/quickwebp.php' === $plugin_file ) {

			$new_actions['settings'] = sprintf(
				// translators: %s is a placeholder for the link to the settings page.
				__( '<a href="%s">Settings</a>', 'quickwebp' ),
				esc_url( admin_url( 'upload.php?page=quickwebp-settings' ) )
			);
		}

		return array_merge( $new_actions, $plugin_actions );
	}

	/**
	 * Display web mode changed
	 */
	public function add_rewrite_rules( $values ) {
		global $is_apache, $is_iis7, $is_nginx;

		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-apache.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-nginx.php';
		include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-iis.php';

		$old_value		= get_option( 'quickwebp_settings_conversion_display_webp_mode', quickwebp_settings_default('quickwebp_settings_conversion_display_webp_mode') );
		$is_rewrite   	= 'rewrite' === $values;
		$was_rewrite  	= 'rewrite' === $old_value;
		$add_or_remove	= false;

		if ( $is_rewrite && !$was_rewrite ) {
			$add_or_remove = 'add';
		} elseif ( !$is_rewrite && $was_rewrite ) {
			$add_or_remove = 'remove';
		} else {
			return $values;
		}

		if ( $is_apache ) {
			$rules = new Quickwebp_Apache();
		} elseif ( $is_iis7 ) {
			$rules = new Quickwebp_IIS();
		} elseif ( $is_nginx ) {
			$rules = new Quickwebp_Nginx();
		} else {
			return $values;
		}

		if ( 'add' === $add_or_remove ) {
			$result = $rules->add();
		} else {
			$result = $rules->remove();
		}

		if ( is_wp_error( $result ) ) {
			add_action( 'admin_notices', function() use ( $result ) {
				?>
					<div class="notice notice-error">
						<p><?php echo esc_html( $result->get_error_message() ); ?></p>
					</div>
				<?php
			});
		}

		return $values;
	}

	/**
	 * Ensure display mode remains consistent with conversion mode.
	 */
	public function sanitize_display_mode_for_consistency( $value, $option = '', $original_value = '' ) {

		$allowed_modes = array( 'disabled', 'picture', 'rewrite' );
		$value         = sanitize_text_field( (string) $value );

		if ( ! in_array( $value, $allowed_modes, true ) ) {
			$value = 'disabled';
		}

		$conversion_enabled = $this->is_conversion_enabled_from_request_or_option();
		if ( ! $conversion_enabled ) {
			return 'disabled';
		}

		$save_original = $this->is_save_original_enabled_from_request_or_option();
		if ( ! $save_original ) {
			return 'disabled';
		}

		if ( 'disabled' === $value ) {
			return 'picture';
		}

		return $value;
	}

	/**
	 * Get whether original images should be preserved.
	 */
	private function is_save_original_enabled_from_request_or_option() {

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['quickwebp_settings_conversion_save_original'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw_value = wp_unslash( $_POST['quickwebp_settings_conversion_save_original'] );

			if ( is_array( $raw_value ) ) {
				$raw_value = array_map( 'sanitize_text_field', $raw_value );
				return in_array( 'checked', $raw_value, true );
			}

			return false;
		}

		$saved_value = get_option(
			'quickwebp_settings_conversion_save_original',
			quickwebp_settings_default( 'quickwebp_settings_conversion_save_original' )
		);

		$saved_value = is_array( $saved_value ) ? $saved_value : array();
		return in_array( 'checked', $saved_value, true );
	}

	/**
	 * Get whether image conversion is enabled.
	 */
	private function is_conversion_enabled_from_request_or_option() {

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['quickwebp_settings_conversion'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw_value = wp_unslash( $_POST['quickwebp_settings_conversion'] );
			$raw_value = sanitize_text_field( (string) $raw_value );

			return '1' === $raw_value;
		}

		$saved_value = get_option(
			'quickwebp_settings_conversion',
			quickwebp_settings_default( 'quickwebp_settings_conversion' )
		);

		return '1' === (string) $saved_value;
	}

	/**
	 * Data for the preview image used in the settings page.
	 */
	private function get_preview_image_data() {
		return array(
			'name'       => 'preview.jpg',
			'size'       => '423.17 KB',
			'dimensions' => '1264 x 848',
			'1_low' => array(
				'size'         => '175.32 KB',
				'save'         => '247.85 KB',
				'save_percent' => '59%',
				'percent'      => '41%',
			),
			'1_medium' => array(
				'size'         => '196.08 KB',
				'save'         => '227.09 KB',
				'save_percent' => '54%',
				'percent'      => '46%',
			),
			'1_high' => array(
				'size'         => '229.29 KB',
				'save'         => '193.88 KB',
				'save_percent' => '46%',
				'percent'      => '54%',
			),
			'1_extra_high' => array(
				'size'         => '387.46 KB',
				'save'         => '35.71 KB',
				'save_percent' => '8%',
				'percent'      => '92%',
			),
			'2_low' => array(
				'size'         => '73.44 KB',
				'save'         => '349.73 KB',
				'save_percent' => '83%',
				'percent'      => '17%',
			),
			'2_medium' => array(
				'size'         => '120.06 KB',
				'save'         => '303.11 KB',
				'save_percent' => '72%',
				'percent'      => '28%',
			),
			'2_high' => array(
				'size'         => '183.21 KB',
				'save'         => '239.96 KB',
				'save_percent' => '57%',
				'percent'      => '43%',
			),
			'2_extra_high' => array(
				'size'         => '290.54 KB',
				'save'         => '132.63 KB',
				'save_percent' => '31%',
				'percent'      => '69%',
			),
		);
	}
}
