<?php
namespace SureCartQuickWebP\Licensing;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The settings class.
 */
class Settings {
	/**
	 * SureCartQuickWebP\Licensing\Client
	 *
	 * @var object
	 */
	protected $client;

	/**
	 * Holds the option key
	 *
	 * @var string
	 */
	private $option_key;

	/**
	 * Holds the option name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Holds the menu arguments
	 *
	 * @var array
	 */
	private $menu_args;

	/**
	 * Create the pages.
	 *
	 * @param SureCartQuickWebP\Licensing\Client $client The client.
	 */
	public function __construct( Client $client ) {
		$this->client     = $client;
		$this->name       = strtolower( preg_replace( '/\s+/', '', $this->client->name ) );
		$this->option_key = $this->name . '_license_options';
		$this->notices    = array();
	}

	/**
	 * Add the settings page.
	 *
	 * @param array $args Settings page args.
	 *
	 * @return void
	 */
	public function add_page( $args ) {
		// store menu args for proper menu creation.
		$this->menu_args = wp_parse_args(
			$args,
			array(
				'type'               => 'menu', // Can be: menu, options, submenu.
				'page_title'         => 'Manage License',
				'menu_title'         => 'Manage License',
				'capability'         => 'manage_options',
				'menu_slug'          => $this->client->slug . '-manage-license',
				'icon_url'           => '',
				'position'           => null,
				'activated_redirect' => null,
				'parent_slug'        => '',
			)
		);
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );
	}

	/**
	 * Form action URL
	 */
	private function form_action_url() {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return apply_filters( 'surecart_client_license_form_action', '' );
	}

	/**
	 * Set the option key.
	 *
	 * If someone wants to override the default generated key.
	 *
	 * @param string $key The option key.
	 */
	public function set_option_key( $key ) {
		$this->option_key = $key;
		return $this;
	}

	/**
	 * Add the admin menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		switch ( $this->menu_args['type'] ) {
			case 'menu':
				$this->create_menu_page();
				break;
			case 'submenu':
				$this->create_submenu_page();
				break;
			case 'options':
				$this->create_options_page();
				break;
		}
	}

	/**
	 * Add license menu page
	 */
	private function create_menu_page() {
		call_user_func(
			'add_menu_page',
			$this->menu_args['page_title'],
			$this->menu_args['menu_title'],
			$this->menu_args['capability'],
			$this->menu_args['menu_slug'],
			array( $this, 'settings_output' ),
			$this->menu_args['icon_url'],
			$this->menu_args['position']
		);
	}

	/**
	 * Add submenu page
	 */
	private function create_submenu_page() {
		call_user_func(
			'add_submenu_page',
			$this->menu_args['parent_slug'],
			$this->menu_args['page_title'],
			$this->menu_args['menu_title'],
			$this->menu_args['capability'],
			$this->menu_args['menu_slug'],
			array( $this, 'settings_output' ),
			$this->menu_args['position']
		);
	}

	/**
	 * Add submenu page
	 */
	private function create_options_page() {
		call_user_func(
			'add_options_page',
			$this->menu_args['page_title'],
			$this->menu_args['menu_title'],
			$this->menu_args['capability'],
			$this->menu_args['menu_slug'],
			array( $this, 'settings_output' ),
			$this->menu_args['position']
		);
	}

	/**
	 * Get all options
	 *
	 * @return array
	 */
	public function get_options() {
		return (array) get_option( $this->option_key, array() );
	}

	/**
	 * Clear out the options.
	 *
	 * @return bool
	 */
	public function clear_options() {
		return update_option( $this->option_key, array() );
	}

	/**
	 * Get a specific option
	 *
	 * @param string $name Option name.
	 *
	 * @return mixed
	 */
	public function get_option( $name ) {
		$options = $this->get_options();
		return isset( $options[ $name ] ) ? $options[ $name ] : null;
	}

	/**
	 * Set the option.
	 *
	 * @param string $name The option name.
	 * @param mixed  $value The option value.
	 *
	 * @return bool
	 */
	public function set_option( $name, $value ) {
		$options          = (array) $this->get_options();
		$options[ $name ] = $value;
		return update_option( $this->option_key, $options );
	}

	/**
	 * The settings page menu output.
	 *
	 * @return void
	 */
	public function settings_output() {
		$this->license_form_submit();

		$assets = include QUICKWEBP_PLUGIN_PATH . 'public/assets/build/licensing.asset.php';
		wp_enqueue_style( 'quickwebp-licensing', QUICKWEBP_PLUGIN_URL . 'public/assets/build/licensing.css', array(), $assets['version'] );

		$activation = $this->get_activation();
		$action     = ! empty( $activation->id ) ? 'deactivate' : 'activate';
		?>

		<div class="wrap">
			<h1></h1>
			<?php settings_errors(); ?>

			<div class="quickwebp-license">
				<form class="quickwebp-license__form" method="post" action="<?php echo esc_attr( $this->form_action_url() ); ?>">
					<input type="hidden" name="_action" value="<?php echo esc_attr( $action ); ?>">
					<input type="hidden" name="_nonce" value="<?php echo esc_attr( wp_create_nonce( $this->client->name ) ); ?>">
					<input type="hidden" name="activation_id" value="<?php echo esc_attr( $this->activation_id ); ?>">

					<h2 class="quickwebp-license__form__title"><?php echo esc_html( $this->menu_args['page_title'] ); ?></h2>

					<div class="quickwebp-license__form__state">
						<div class="quickwebp-license__form__state__left">
							<?php if ( 'activate' === $action ) : ?> 
								<?php echo esc_html( sprintf(
									// translators: %s is a placeholder for the name of the client.
									__( 'Enter your license key to activate %s.', 'quickwebp' ),
									$this->client->name ) 
								); ?>
							<?php else : ?>
								<?php echo esc_html( sprintf(
									// translators: %s is a placeholder for the name of the client.
									__( 'Your license is successfully activated for this site.', 'quickwebp' ),
									$this->client->name ) 
								); ?>
							<?php endif; ?>
						</div>
						<div class="quickwebp-license__form__state__right">
							<div class="quickwebp-license__form__state__right__text"><?php esc_html_e( 'State', 'quickwebp' ); ?>:</div>
							<div class="quickwebp-license__form__state__right__icon <?php echo esc_attr( $action ); ?>"></div>
						</div>
					</div>

					<div class="quickwebp-license__form__input">
						<?php if ( 'activate' === $action ) : ?>
							<input class="widefat" type="password" autocomplete="off" name="license_key" id="license_key" value="<?php echo esc_attr( $this->license_key ); ?>" autofocus placeholder="<?php esc_attr_e( 'Enter the license...', 'quickwebp' ); ?>">
						<?php else : ?>
							<input class="widefat license-key-masked" type="text" value="<?php echo esc_attr( substr( $this->license_key, 0, 4 ) . '******************' ); ?>" disabled>
						<?php endif; ?>
					</div>

					<div class="quickwebp-license__form__actions">
						<div class="quickwebp-license__form__actions__left">
							<?php if ( 'activate' === $action ) : ?>
								<button name="submit" type="submit" class="quickwebp-license__form__actions__left__activate">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/key.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
									<?php esc_html_e( 'Activate the license', 'quickwebp' ); ?>
								</button>
							<?php else : ?>
								<button name="submit" type="submit" class="quickwebp-license__form__actions__left__deactivate">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/key-disable.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
									<?php esc_html_e( 'Deactivate the license', 'quickwebp' ); ?>
								</button>
							<?php endif; ?>
						</div>
						<div class="quickwebp-license__form__actions__right">
							<a href="<?php echo esc_url( \Quickwebp_Settings::QUICKWEBP_GET_MY_ACCOUNT_URL ); ?>" target="_blank" class="quickwebp-license__form__actions__right__link">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/admin-alt.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								<?php esc_html_e( 'My account', 'quickwebp' ); ?>
							</a>
						</div>
					</div>
				</form>

				<?php if ( 'activate' === $action ) : ?> 
				<div class="quickwebp-license__pro">
					<div class="quickwebp-license__pro__header">
						<div class="quickwebp-license__pro__header__text">
							<div class="quickwebp-license__pro__header__text__subtitle">
								<div class="quickwebp-license__pro__header__text__subtitle__tag"><?php esc_html_e( 'Pro', 'quickwebp' ); ?></div>
								<div class="quickwebp-license__pro__header__text__subtitle__text"><?php esc_html_e( 'AVIF Format', 'quickwebp' ); ?></div>
							</div>
							<div class="quickwebp-license__pro__header__text__title">
								<?php esc_html_e( 'Go further with', 'quickwebp' ); ?>
								<span class="highlight"> AVIF</span>
							</div>
							<div class="quickwebp-license__pro__header__text__desc">
								<?php esc_html_e( 'The next-generation image format, available with', 'quickwebp' ); ?>
								<span class="highlight">QuickWebP Pro.</span>
							</div>
						</div>
					</div>

					<div class="quickwebp-license__pro__body">
						<div class="quickwebp-license__pro__body__top">
							<div class="quickwebp-license__pro__body__top__item">
								<div class="quickwebp-license__pro__body__top__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/gauge.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__top__item__text">
									<div class="quickwebp-license__pro__body__top__item__text__title">-50%</div>
									<div class="quickwebp-license__pro__body__top__item__text__desc"><?php esc_html_e( 'Vs jpeg/png on average', 'quickwebp' ); ?></div>
								</div>
							</div>

							<div class="quickwebp-license__pro__body__top__item second">
								<div class="quickwebp-license__pro__body__top__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/chart-line.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__top__item__text">
									<div class="quickwebp-license__pro__body__top__item__text__title">-30%</div>
									<div class="quickwebp-license__pro__body__top__item__text__desc"><?php esc_html_e( 'Vs webp on average', 'quickwebp' ); ?></div>
								</div>
							</div>

							<div class="quickwebp-license__pro__body__top__item third">
								<div class="quickwebp-license__pro__body__top__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/global.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__top__item__text">
									<div class="quickwebp-license__pro__body__top__item__text__title">100%</div>
									<div class="quickwebp-license__pro__body__top__item__text__desc"><?php esc_html_e( 'of modern browsers supported', 'quickwebp' ); ?></div>
								</div>
							</div>
						</div>

						<div class="quickwebp-license__pro__body__middle">
							<div class="quickwebp-license__pro__body__middle__item">
								<div class="quickwebp-license__pro__body__middle__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__middle__item__text">
									<strong><?php echo wp_kses_post(
										// translators: %s is a placeholder for the text "</strong>".
										sprintf( __( 'The best compression available %s images typically half the size of your originals.', 'quickwebp' ), '</strong>' )
									); ?>
								</div>
							</div>

							<div class="quickwebp-license__pro__body__middle__item">
								<div class="quickwebp-license__pro__body__middle__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__middle__item__text">
									<strong><?php echo wp_kses_post(
										// translators: %s is a placeholder for the text "</strong>".
										sprintf( __( 'Sharper images at lower weight %s for faster pages and better Core Web Vitals.', 'quickwebp' ), '</strong>' )
									); ?>
								</div>
							</div>

							<div class="quickwebp-license__pro__body__middle__item">
								<div class="quickwebp-license__pro__body__middle__item__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</div>
								<div class="quickwebp-license__pro__body__middle__item__text">
									<strong><?php echo wp_kses_post(
										// translators: %s is a placeholder for the text "</strong>".
										sprintf( __( 'Automatic fallback %s so older browsers keep receiving a compatible format.', 'quickwebp' ), '</strong>' )
									); ?>
								</div>
							</div>

						</div>

						<div class="quickwebp-license__pro__body__bottom">
							<div class="quickwebp-license__pro__body__bottom__upgrade">
								<a href="<?php echo esc_url( quickwebp_get_pro_url( 'license-screen-upgrade' ) ); ?>" class="quickwebp-license__pro__body__bottom__upgrade__link">
									<span class="quickwebp-license__pro__body__bottom__upgrade__link__icon">
										<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/crown.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
									</span>
									<?php esc_html_e( 'Upgrade to QuickWebP Pro', 'quickwebp' ); ?>
								</a>
							</div>
						</div>
					</div>

				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the activation.
	 *
	 * @return Object|false
	 */
	public function get_activation() {
		$activation = false;
		if ( $this->activation_id ) {
			$activation = $this->client->activation()->get( $this->activation_id );
			if ( is_wp_error( $activation ) ) {
				if ( 'not_found' === $activation->get_error_code() ) {
					$this->add_error( 'deactivaed', __( 'Your license has been deactivated for this site.', 'quickwebp' ) );
					$this->clear_options();
				}
			}
		}
		return $activation;
	}

	/**
	 * License form submit
	 */
	public function license_form_submit() {
		// only if we are submitting.
		if ( ! isset( $_POST['submit'] ) ) {
			return;
		}

		// Check nonce.
		if ( ! isset( $_POST['_nonce'], $_POST['_action'] ) ) {
			$this->add_error( 'missing_info', __( 'Please add all information', 'quickwebp' ) );
			return;
		}

		// Cerify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), $this->client->name ) ) {
			$this->add_error( 'unauthorized', __( "You don't have permission to manage licenses.", 'quickwebp' ) );
			return;
		}

		// handle activation.
		if ( 'activate' === sanitize_text_field( wp_unslash( $_POST['_action'] ) ) ) {
			$activated = $this->client->license()->activate( sanitize_text_field( wp_unslash( $_POST['license_key'] ?? '' ) ) );
			if ( is_wp_error( $activated ) ) {
				$this->add_error( $activated->get_error_code(), $activated->get_error_message() );
				return;
			}

			if ( ! empty( $this->menu_args['activated_redirect'] ) ) {
				$this->redirect( $this->menu_args['activated_redirect'] );
				exit;
			}

			return $this->add_success( 'activated', __( 'This site was successfully activated.', 'quickwebp' ) );
		}

		// handle deactivation.
		if ( 'deactivate' === sanitize_text_field( wp_unslash( $_POST['_action'] ) ) ) {
			$deactivated = $this->client->license()->deactivate( sanitize_text_field( wp_unslash( $_POST['activation_id'] ?? '' ) ) );
			if ( is_wp_error( $deactivated ) ) {
				$this->add_error( $deactivated->get_error_code(), $deactivated->get_error_message() );
			}

			if ( ! empty( $this->menu_args['deactivated_redirect'] ) ) {
				$this->redirect( $this->menu_args['deactivated_redirect'] );
				exit;
			}

			return $this->add_success( 'deactivated', __( 'This site was successfully deactivated.', 'quickwebp' ) );
		}
	}

	/**
	 * Redirect to a url client-side.
	 * We need to do this to avoid "headers already sent" messages.
	 *
	 * @param string $url Url to redirect.
	 *
	 * @return void
	 */
	public function redirect( $url ) {
		?>
		<div class="spinner is-active"></div>
		<script>
			window.location.assign("<?php echo esc_url( $url ); ?>");
		</script>
		<?php
	}

	/**
	 * Add a notice.
	 *
	 * @param string $code Notice code.
	 * @param string $message Notice message.
	 * @param string $type Notice type.
	 *
	 * @return void
	 */
	public function add_notice( $code, $message, $type = 'info' ) {
		add_settings_error(
			$this->name . '_license_options', // matches what we registered in `register_setting.
			$code, // the error code.
			$message,
			$type
		);
	}

	/**
	 * Add an error.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	public function add_error( $code, $message ) {
		$this->add_notice( $code, $message, 'error' );
	}

	/**
	 * Add an success message
	 *
	 * @param string $code Success code.
	 * @param string $message Success message.
	 *
	 * @return void
	 */
	public function add_success( $code, $message ) {
		$this->add_notice( $code, $message, 'success' );
	}

	/**
	 * Set an option.
	 *
	 * @param string $name Name of option.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->get_option( 'sc_' . $name );
	}

	/**
	 * Set an option
	 *
	 * @param string $name Name of option.
	 * @param mixed  $value Value.
	 *
	 * @return bool
	 */
	public function __set( $name, $value ) {
		return $this->set_option( 'sc_' . $name, $value );
	}
}
