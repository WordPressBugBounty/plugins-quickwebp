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
		$this->maybe_migrate_optimization_stats();

		$last_version = get_option( 'quickwebp_plugin_version', '1.0.0' );

		if ( version_compare( $last_version, QUICKWEBP_VERSION, '<' ) ) {
			$this->run_migration_tasks( $last_version );
			update_option( 'quickwebp_plugin_version', QUICKWEBP_VERSION );
		}
	}

	/**
	 * Backfill installation age and optimization statistics for existing sites.
	 */
	private function maybe_migrate_optimization_stats() {
		if ( get_option( 'quickwebp_optimization_stats_migrated', false ) ) {
			return;
		}

		$stats = array(
			'images_optimized' => 0,
			'bytes_before'     => 0,
			'bytes_after'      => 0,
			'bytes_saved'      => 0,
		);
		$page  = 1;

		do {
			$query = new WP_Query(
				array(
					'post_type'              => 'attachment',
					'post_status'            => 'any',
					'posts_per_page'         => 200,
					'paged'                  => $page,
					'fields'                 => 'ids',
					'orderby'                => 'ID',
					'order'                  => 'ASC',
					'no_found_rows'          => false,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
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

			foreach ( $query->posts as $attachment_id ) {
				$data = get_post_meta( $attachment_id, 'quickwebp_data', true );

				$stats['images_optimized']++;

				if ( ! is_array( $data ) ) {
					continue;
				}

				foreach ( $data as $size_data ) {
					$stats['bytes_before'] += absint( $size_data['original_size'] ?? 0 );
					$stats['bytes_after']  += absint( $size_data['optimized_size'] ?? 0 );
				}
			}

			$max_pages = (int) $query->max_num_pages;
			$page++;
		} while ( $page <= $max_pages );

		$stats['bytes_saved'] = max( 0, $stats['bytes_before'] - $stats['bytes_after'] );

		if ( $stats['images_optimized'] > 0 ) {
			update_option( 'quickwebp_optimization_stats', $stats, false );
		}

		if ( ! get_option( 'quickwebp_installed_at', false ) ) {
			$oldest_optimized = get_posts(
				array(
					'post_type'              => 'attachment',
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'orderby'                => 'date',
					'order'                  => 'ASC',
					'fields'                 => 'ids',
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'meta_query'             => array(
						array(
							'key'     => 'quickwebp_already_optimized',
							'compare' => 'EXISTS',
						),
					),
				)
			);

			if ( ! empty( $oldest_optimized ) ) {
				$installed_at = get_post_time( 'U', true, $oldest_optimized[0] );
			} else {
				$installed_at = time() - ( 8 * DAY_IN_SECONDS );
			}

			add_option( 'quickwebp_installed_at', $installed_at, '', false );
		}

		update_option( 'quickwebp_optimization_stats_migrated', 1, false );
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
