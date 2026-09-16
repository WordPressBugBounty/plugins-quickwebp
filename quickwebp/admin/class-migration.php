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
	 * Last attachment ID processed by the current migration batch.
	 *
	 * @var int
	 */
	private $migration_cursor = 0;

	/**
	 * Init the migration.
	 */
	public function init_migration() {
		$this->maybe_schedule_optimization_stats_migration();

		$last_version = get_option( 'quickwebp_plugin_version', '1.0.0' );

		if ( version_compare( $last_version, QUICKWEBP_VERSION, '<' ) ) {
			$this->run_migration_tasks( $last_version );
			update_option( 'quickwebp_plugin_version', QUICKWEBP_VERSION );
		}
	}

	/**
	 * Schedule the optimization statistics migration outside the admin request.
	 */
	private function maybe_schedule_optimization_stats_migration() {
		if ( get_option( 'quickwebp_optimization_stats_migrated', false ) ) {
			return;
		}

		if ( ! wp_next_scheduled( 'quickwebp_optimization_stats_migration_hook' ) ) {
			wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'quickwebp_optimization_stats_migration_hook' );
		}
	}

	/**
	 * Process one bounded batch of the optimization statistics migration.
	 */
	public function migrate_optimization_stats_batch() {
		if ( get_option( 'quickwebp_optimization_stats_migrated', false ) ) {
			return;
		}

		$lock_token = $this->acquire_migration_lock();
		if ( ! $lock_token ) {
			$this->schedule_next_migration_batch();
			return;
		}

		$state                  = get_option( 'quickwebp_optimization_stats_migration_state', array() );
		$state                  = wp_parse_args(
			is_array( $state ) ? $state : array(),
			array(
				'cursor'           => 0,
				'images_optimized' => 0,
				'bytes_before'     => 0,
				'bytes_after'      => 0,
				'oldest_time'      => 0,
			)
		);
		$this->migration_cursor = absint( $state['cursor'] );

		add_filter( 'posts_where', array( $this, 'filter_migration_posts_after_cursor' ), 10, 2 );
		$query = new WP_Query(
			array(
				'post_type'              => 'attachment',
				'post_status'            => 'any',
				'posts_per_page'         => 50,
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
				'quickwebp_migration'    => true,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'             => array(
					'relation' => 'OR',
					array(
						'key'     => 'quickwebp_already_optimized',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'quickwebp_data',
						'compare' => 'EXISTS',
					),
				),
			)
		);
		remove_filter( 'posts_where', array( $this, 'filter_migration_posts_after_cursor' ), 10 );

		foreach ( $query->posts as $attachment_id ) {
			$data                     = get_post_meta( $attachment_id, 'quickwebp_data', true );
			$state['cursor']          = $attachment_id;
			$state['images_optimized']++;
			$attachment_time          = (int) get_post_time( 'U', true, $attachment_id );
			$state['oldest_time']     = ! $state['oldest_time'] ? $attachment_time : min( $state['oldest_time'], $attachment_time );

			if ( ! is_array( $data ) ) {
				continue;
			}

			foreach ( $data as $size_data ) {
				$state['bytes_before'] += absint( $size_data['original_size'] ?? 0 );
				$state['bytes_after']  += absint( $size_data['optimized_size'] ?? 0 );
			}
		}

		if ( count( $query->posts ) === 50 ) {
			update_option( 'quickwebp_optimization_stats_migration_state', $state, false );
			$this->release_migration_lock( $lock_token );
			$this->schedule_next_migration_batch();
			return;
		}

		$stats = array(
			'images_optimized' => absint( $state['images_optimized'] ),
			'bytes_before'     => absint( $state['bytes_before'] ),
			'bytes_after'      => absint( $state['bytes_after'] ),
			'bytes_saved'      => max( 0, absint( $state['bytes_before'] ) - absint( $state['bytes_after'] ) ),
		);

		if ( $stats['images_optimized'] > 0 ) {
			update_option( 'quickwebp_optimization_stats', $stats, false );
		}

		if ( ! get_option( 'quickwebp_installed_at', false ) ) {
			$installed_at = $state['oldest_time'] ? absint( $state['oldest_time'] ) : time() - ( 8 * DAY_IN_SECONDS );
			add_option( 'quickwebp_installed_at', $installed_at, '', false );
		}

		update_option( 'quickwebp_optimization_stats_migrated', 1, false );
		delete_option( 'quickwebp_optimization_stats_migration_state' );
		$this->release_migration_lock( $lock_token );
	}

	/**
	 * Restrict the migration query to attachment IDs after the saved cursor.
	 *
	 * @param string   $where Query WHERE clause.
	 * @param WP_Query $query Current query.
	 * @return string
	 */
	public function filter_migration_posts_after_cursor( $where, $query ) {
		global $wpdb;

		if ( $query->get( 'quickwebp_migration' ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.ID > %d", $this->migration_cursor );
		}

		return $where;
	}

	/**
	 * Schedule the next migration batch unless one is already pending.
	 */
	private function schedule_next_migration_batch() {
		if ( ! wp_next_scheduled( 'quickwebp_optimization_stats_migration_hook' ) ) {
			wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'quickwebp_optimization_stats_migration_hook' );
		}
	}

	/**
	 * Acquire an expiring migration lock.
	 *
	 * @return string|false
	 */
	private function acquire_migration_lock() {
		$lock_token = wp_generate_uuid4();
		$lock       = array(
			'token'   => $lock_token,
			'expires' => time() + ( 5 * MINUTE_IN_SECONDS ),
		);

		if ( add_option( 'quickwebp_optimization_stats_migration_lock', $lock, '', false ) ) {
			return $lock_token;
		}

		$existing_lock = get_option( 'quickwebp_optimization_stats_migration_lock', array() );
		if ( is_array( $existing_lock ) && absint( $existing_lock['expires'] ?? 0 ) < time() ) {
			delete_option( 'quickwebp_optimization_stats_migration_lock' );
			if ( add_option( 'quickwebp_optimization_stats_migration_lock', $lock, '', false ) ) {
				return $lock_token;
			}
		}

		return false;
	}

	/**
	 * Release the migration lock owned by this process.
	 *
	 * @param string $lock_token Lock ownership token.
	 */
	private function release_migration_lock( $lock_token ) {
		$lock = get_option( 'quickwebp_optimization_stats_migration_lock', array() );
		if ( is_array( $lock ) && hash_equals( (string) ( $lock['token'] ?? '' ), $lock_token ) ) {
			delete_option( 'quickwebp_optimization_stats_migration_lock' );
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
