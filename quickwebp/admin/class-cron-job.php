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
class Quickwebp_Cron_Job {

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
	 * @param      string   $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register cron jobs
	 */
	public function crons_registrations( $schedules ) {
		
		$schedules['bulk_optimization'] = array(
			'interval' => MINUTE_IN_SECONDS,
			'display'  => __( 'Every minute', 'quickwebp' )
		);

		return $schedules;
	}

	/**
	 * excute bulk optimization
	 */
	public function excute_bulk_optimization() {
		$time_start = microtime(true);

		$quickwebp_image_optimizer = new Quickwebp_Image_Optimizer( QUICKWEBP_TEXT_DOMAIN, QUICKWEBP_VERSION );
		$settings                  = $quickwebp_image_optimizer->get_settings();

		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( '0' === $mode_enabled ) {
			$this->end_cron_job();
		}

		$media_ids = $quickwebp_image_optimizer->get_unoptimized_media_ids();
		if ( empty( $media_ids ) ) {
			$this->end_cron_job();
		}

		$status = get_option( 'quickwebp_bulk_optimize_status', 'finish' );
		if ( $status != 'running' ) {
			$this->end_cron_job();
		}
		
		$current = (int)get_option( 'quickwebp_bulk_optimize_current', 0 );

		foreach ( $media_ids as $id ) {
			if ( $time_start + 55 < microtime(true) ) {
				exit;
			}

			$sizes     = $quickwebp_image_optimizer->get_media_files( $id );
    		$new_sizes = array();

			foreach ( $sizes as $key => $size ) {
				$result = $quickwebp_image_optimizer->optimize_local_file( $size );
		
				if ( $result ) {
					$new_sizes[$key] = $result;
				}
			}

			if ( ! empty( $new_sizes ) ) {

				$data = get_post_meta( $id, 'quickwebp_data', true );
				if ( ! empty( $data ) ) {
					$quickwebp_image_optimizer->remove_related_files( $data );
				}

				if ( '1' == $mode_enabled ) {
					update_post_meta( $id, 'quickwebp_already_optimized', '1' );
				} elseif ( '2' == $mode_enabled ) {
					update_post_meta( $id, 'quickwebp_already_optimized', '2' );
				}

				update_post_meta( $id, 'quickwebp_data', $new_sizes );
				delete_post_meta( $id, 'quickwebp_has_error' );
			} else {
				update_post_meta( $id, 'quickwebp_has_error', '1' );
				delete_post_meta( $id, 'quickwebp_already_optimized' );
				delete_post_meta( $id, 'quickwebp_data' );
			}

			$current++;
			update_option( 'quickwebp_bulk_optimize_current', $current );
		}

		$this->end_cron_job();
	}

	/**
	 * Start bulk optimization
	 */
	public function start_bulk_optimization() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to manage plugin settings.', 'quickwebp' ), 403 );
		}

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		$quickwebp_image_optimizer = new Quickwebp_Image_Optimizer( QUICKWEBP_TEXT_DOMAIN, QUICKWEBP_VERSION );
		$settings                  = $quickwebp_image_optimizer->get_settings();

		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( '0' === $mode_enabled ) {
			wp_send_json_error( __( 'Choose an image format and save the settings.', 'quickwebp' ) );
		}

		$media_ids = $quickwebp_image_optimizer->get_unoptimized_media_ids();
		if ( empty( $media_ids ) ) {
			wp_send_json_error( __( 'No images to optimize.', 'quickwebp' ) );
		}

		$status = get_option( 'quickwebp_bulk_optimize_status', 'finish' );
		if ( $status != 'finish' ) {
			wp_send_json_error( __( 'Bulk optimization is already running.', 'quickwebp' ) );
		}

		if ( ! wp_next_scheduled( 'quickwebp_bulk_optimization_hook' ) ) {
			wp_schedule_event( time(), 'bulk_optimization', 'quickwebp_bulk_optimization_hook' );

			$total   = count( $media_ids );
			$current = 0;

			update_option( 'quickwebp_bulk_optimize_total', $total );
			update_option( 'quickwebp_bulk_optimize_current', $current );
			update_option( 'quickwebp_bulk_optimize_status', 'running' );

			$data = array(
				'progress' => $current . '/' . $total,
				'percent'  => $total ? round( abs( ( $current / $total ) ) * 100 ) . '%' : '0%'
			);
			wp_send_json_success( $data );
		}

		wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
	}

	/**
	 * Stop bulk optimization
	 */
	public function stop_bulk_optimization() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to manage plugin settings.', 'quickwebp' ), 403 );
		}
		
		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		if ( $this->clear_bulk_optimization() ) {
			wp_send_json_success( __( 'Bulk optimization stopped.', 'quickwebp' ) );
		}

		wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
	}

	/**
	 * Check bulk optimization progress
	 */
	public function check_bulk_optimization_progress() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to manage plugin settings.', 'quickwebp' ), 403 );
		}

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		$status 	= get_option( 'quickwebp_bulk_optimize_status', '' );
		$total 		= (int)get_option( 'quickwebp_bulk_optimize_total', 0 );
		$current	= (int)get_option( 'quickwebp_bulk_optimize_current', 0 );
		$is_running = $status == 'running' ? true : false;

		$data = array(
			'running'	=> $is_running,
			'progress'	=> $current . '/' . $total,
			'percent'	=> $total ? round( abs( ( $current / $total ) ) * 100 ) . '%' : '0%'
		);
		wp_send_json_success( $data );
	}

	/**
	 * Clear bulk optimization
	 */
	private function clear_bulk_optimization() {
		update_option( 'quickwebp_bulk_optimize_status', 'finish' );

		if ( wp_next_scheduled( 'quickwebp_bulk_optimization_hook' ) ) {
			wp_clear_scheduled_hook( 'quickwebp_bulk_optimization_hook' );
			return true;
		}

		return false;
	}

	/**
	 * End cron job
	 */
	private function end_cron_job() {
		$this->clear_bulk_optimization();
		exit;
	}
}
