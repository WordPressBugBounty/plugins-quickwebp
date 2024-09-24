<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Quickwebp
 * @subpackage Quickwebp/includes
 * @author     Webdeclic <contact@webdeclic.com>
 */
class Quickwebp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Quickwebp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'QUICKWEBP_VERSION' ) ) {
			$this->version = QUICKWEBP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'quickwebp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Quickwebp_Loader. Orchestrates the hooks of the plugin.
	 * - Quickwebp_i18n. Defines internationalization functionality.
	 * - Quickwebp_Admin. Defines all hooks for the admin area.
	 * - Quickwebp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for loading composer dependencies.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'includes/vendor/autoload.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'includes/class-quickwebp-loader.php';

		/**
		 * This file is loaded only on local environement for test or debug.
		 */
		if( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' ){
			require_once QUICKWEBP_PLUGIN_PATH. 'includes/dev-toolkits.php';
		}
		
		/**
		 * The global functions for this plugin
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'includes/global-functions.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'includes/class-quickwebp-i18n.php';

		/**
		 * The class responsible of settings.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'admin/class-settings.php';

		/**
		 * The core functionality for image optimizing
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'admin/class-image-optimizer.php';
		
		/**
		 * The core functionality for wp media extending
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'admin/class-wp-media-extends.php';

		/**
		 * The class responsible of cron job.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'admin/class-cron-job.php';

		/**
		 * The handle the front-end display functionality of the plugin.
		 */
		require_once QUICKWEBP_PLUGIN_PATH . 'public/class-display-webp.php';

		$this->loader = new Quickwebp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Quickwebp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Quickwebp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$quickwebp_settings = new Quickwebp_Settings( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $quickwebp_settings, 'enqueue_scripts_styles' );
		$this->loader->add_action( 'admin_menu', $quickwebp_settings, 'add_settings_menu' );
		$this->loader->add_filter( 'plugin_action_links', $quickwebp_settings, 'add_settings_link', 10, 2 );
		$this->loader->add_action( 'admin_init', $quickwebp_settings, 'show_notice_if_library_not_exist' );
		$this->loader->add_filter( 'sanitize_option_quickwebp_settings_conversion_display_webp_mode', $quickwebp_settings, 'add_rewrite_rules', 5 );
		
		$quickwebp_image_optimizer = new Quickwebp_Image_Optimizer( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'wp_handle_upload_prefilter', $quickwebp_image_optimizer, 'image_optimizition' );
		$this->loader->add_filter( 'wp_generate_attachment_metadata', $quickwebp_image_optimizer, 'add_data_to_attachment', 10, 3 );
		$this->loader->add_filter( 'big_image_size_threshold', $quickwebp_image_optimizer, 'change_wp_max_size', PHP_INT_MAX, 4 );
		$this->loader->add_filter( 'wp_editor_set_quality', $quickwebp_image_optimizer, 'change_wp_quality', PHP_INT_MAX, 2 );
		$this->loader->add_action( 'wp_ajax_image_optimizition_ajax', $quickwebp_image_optimizer, 'image_optimizition_ajax' );
		$this->loader->add_action( 'wp_ajax_single_optimizition_ajax', $quickwebp_image_optimizer, 'single_optimizition_ajax' );
		$this->loader->add_action( 'wp_ajax_undo_single_optimizition_ajax', $quickwebp_image_optimizer, 'undo_single_optimizition_ajax' );
		$this->loader->add_action( 'delete_attachment', $quickwebp_image_optimizer, 'before_delete_attachment', 10, 2 );

		$quickwebp_wp_media_extends = new Quickwebp_Wp_Media_Extends( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_media', $quickwebp_wp_media_extends, 'enqueue_scripts' );
		$this->loader->add_filter( 'attachment_fields_to_edit', $quickwebp_wp_media_extends, 'add_attachment_fields_to_edit', PHP_INT_MAX, 2 );
		$this->loader->add_filter( 'manage_media_columns', $quickwebp_wp_media_extends, 'add_media_columns');
		$this->loader->add_action( 'manage_media_custom_column', $quickwebp_wp_media_extends, 'add_media_custom_column', 10, 2 );
		$this->loader->add_action( 'attachment_submitbox_misc_actions', $quickwebp_wp_media_extends, 'add_attachment_submitbox_misc_actions', PHP_INT_MAX );

		$quickwebp_cron_job = new Quickwebp_Cron_Job( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'cron_schedules', $quickwebp_cron_job, 'crons_registrations' );
		$this->loader->add_action( 'quickwebp_bulk_optimization_hook', $quickwebp_cron_job, 'excute_bulk_optimization' );
		$this->loader->add_action( 'wp_ajax_start_bulk_optimization', $quickwebp_cron_job, 'start_bulk_optimization' );
		$this->loader->add_action( 'wp_ajax_stop_bulk_optimization', $quickwebp_cron_job, 'stop_bulk_optimization' );
		$this->loader->add_action( 'wp_ajax_check_bulk_optimization_progress', $quickwebp_cron_job, 'check_bulk_optimization_progress' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$quickwebp_display_webp = new Quickwebp_Display_Webp( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'template_redirect', $quickwebp_display_webp, 'start_content_process', -1000 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Quickwebp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
