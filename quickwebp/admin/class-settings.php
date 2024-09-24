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
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'image_optimize_nonce' ),
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
			__('Quickwebp Settings', QUICKWEBP_TEXT_DOMAIN),
			__('Quickwebp', QUICKWEBP_TEXT_DOMAIN),
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
		global $is_nginx;

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

			$new_actions['settings'] = sprintf( __( '<a href="%s">Settings</a>', QUICKWEBP_TEXT_DOMAIN ),  esc_url( admin_url( 'upload.php?page=quickwebp-settings' ) ) );
		}

		return array_merge( $new_actions, $plugin_actions );
	}

	/**
	 * Show notice
	 */
	public function show_notice_if_library_not_exist() {

		$library_to_use = get_option( 'quickwebp_settings_library', quickwebp_settings_default('quickwebp_settings_library') );

		switch ($library_to_use) {
			case 'gd':

				if ( ! extension_loaded('gd') ) {
					add_action( 'admin_notices', function() {
						?>
							<div class="notice notice-error">
								<p><?php _e( 'Oops! QuickWebP needs the GD library to function properly, but it looks like the library is not installed on your server.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
								<p><?php _e( 'Please contact your hosting provider and ask them to install the GD library for PHP. They should be able to assist you with this process. Once the library is installed, QuickWebP should work as expected.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
								<p><?php _e( 'Thank you for using QuickWebP!', QUICKWEBP_TEXT_DOMAIN ); ?></p>
							</div>
						<?php
					});
				}

			break;

			case 'imagick':

				if ( ! extension_loaded('imagick') ) {
					add_action( 'admin_notices', function() {
						?>
							<div class="notice notice-error">
								<p><?php _e( 'Oops! QuickWebP needs the Imagick library to function properly, but it looks like the library is not installed on your server.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
								<p><?php _e( 'Please contact your hosting provider and ask them to install the Imagick library for PHP. They should be able to assist you with this process. Once the library is installed, QuickWebP should work as expected.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
								<p><?php _e( 'Thank you for using QuickWebP!', QUICKWEBP_TEXT_DOMAIN ); ?></p>
							</div>
						<?php
					});
				}
				
			break;
			
			default:

				add_action( 'admin_notices', function() {
					?>
						<div class="notice notice-error">
							<p><?php _e( 'Oops! QuickWebP needs the Imagick or GD library to function properly, but it looks like the no Imagick nor GD is not installed on your server.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
							<p><?php _e( 'Please contact your hosting provider and ask them to install the Imagick or GD library for PHP. They should be able to assist you with this process. Once the library is installed, QuickWebP should work as expected.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
							<p><?php _e( 'Thank you for using QuickWebP!', QUICKWEBP_TEXT_DOMAIN ); ?></p>
						</div>
					<?php
				});
				
			break;
		}

		
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
			$rules = new Apache();
		} elseif ( $is_iis7 ) {
			$rules = new IIS();
		} elseif ( $is_nginx ) {
			$rules = new Nginx();
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
						<p><?php echo $result->get_error_message(); ?></p>
					</div>
				<?php
			});
		}

		return $values;
	}

}