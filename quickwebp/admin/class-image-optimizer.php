<?php

/**
 * The core functionality for image optimizing.
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Image_Optimizer {

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
	 * Allowed mime types for the optimazation
	 */
	public $allowed_mime_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->allowed_mime_types = array( 'image/jpeg', 'image/png', 'image/webp', 'image/avif' );
	}

	/**
	 * Optimize an image added in wp_media
	 */
	public function image_optimization( $file ) {
		global $quickwebp_surecart_client;

		$settings	= $this->get_settings();
		$image_file = $this->file_is_image( $file, $settings );

		if ( ! $image_file ) {
			return $file;
		}

		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( $mode_enabled === '0' ) {
			return $image_file;
		}

		$save_original = is_array( $settings['quickwebp_settings_conversion_save_original'] ) ? $settings['quickwebp_settings_conversion_save_original'] : array();
		if ( in_array( 'checked', $save_original ) ) {
			return $image_file;
		}

		$ignore_same_format = is_array( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? $settings['quickwebp_settings_conversion_ignore_webp'] : array();
		$mime_type          = wp_get_image_mime( $image_file['tmp_name'] );
		if ( in_array( 'checked', $ignore_same_format ) ) {
			if ( '1' == $mode_enabled && 'image/webp' == $mime_type ) {
				return $image_file;
			} elseif( '2' == $mode_enabled && 'image/avif' == $mime_type ) {
				return $image_file;
			}
		}

		$quality = $this->get_the_quality( $settings );
		$is_pro  = false;
		if ( $quickwebp_surecart_client ) {
			$is_pro = $quickwebp_surecart_client->license()->is_valid();
		}

		$image_file['new_size'] = $image_file['size'];
		$image_file['new_type'] = $image_file['type'];

		if ( '2' === $mode_enabled && $is_pro ) {
			$avif_image = $this->create_avif_image( $image_file['tmp_name'], $image_file['tmp_name'], $quality );
			if ( $avif_image ) {
				$image_file['size']      = filesize( $image_file['tmp_name'] );
				$image_file['type']      = 'image/avif';
				$image_file['quickwebp'] = 'optimized';
			}
		}

		if ( '1' === $mode_enabled ) {
			$webp_image = $this->create_webp_image( $image_file['tmp_name'], $image_file['tmp_name'], $quality );
			if ( $webp_image ) {
				$image_file['size']      = filesize( $image_file['tmp_name'] );
				$image_file['type']      = 'image/webp';
				$image_file['quickwebp'] = 'optimized';
			}
		}

		return $image_file;
	}

	/**
	 * Add post meta to the attachment that already optimized
	 */
	public function add_already_optimized_meta( $metadata, $attachment_id, $context ) {
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$quickwebp = sanitize_text_field( wp_unslash( $_FILES['async-upload']['quickwebp'] ?? '' ) );
		
		if ( 'optimized' == $quickwebp ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Missing
			$type = sanitize_text_field( wp_unslash( $_FILES['async-upload']['type'] ?? '' ) );

			if ( 'image/webp' == $type ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '1' );
			} elseif ( 'image/avif' == $type ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '2' );
			}
		}

		return $metadata;
	}

	/**
	 * Save the original image in the attachment meta
	 */
	public function save_original_image( $metadata, $attachment_id, $context ) {

		if ( $context != 'create' ) {
			return $metadata;
		}

		$settings = $this->get_settings();

		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( $mode_enabled === '0' ) {
			return $metadata;
		}

		$save_original = is_array( $settings['quickwebp_settings_conversion_save_original'] ) ? $settings['quickwebp_settings_conversion_save_original'] : array();
		if ( ! in_array( 'checked', $save_original ) ) {
			return $metadata;
		}

		$ignore_same_format = is_array( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? $settings['quickwebp_settings_conversion_ignore_webp'] : array();
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$mime_type          = sanitize_text_field( wp_unslash( $_FILES['async-upload']['type'] ?? '' ) );
		if ( in_array( 'checked', $ignore_same_format ) ) {
			if ( '1' == $mode_enabled && 'image/webp' == $mime_type ) {
				return $metadata;
			} elseif( '2' == $mode_enabled && 'image/avif' == $mime_type ) {
				return $metadata;
			}
		}

		$sizes     = $this->get_media_files( $attachment_id );
		$new_sizes = array();
		
		foreach ( $sizes as $key => $size ) {
			$result = $this->optimize_local_file( $size );

			if ( $result ) {
				$new_sizes[$key] = $result;
			}
		}

		if ( ! empty( $new_sizes ) ) {
			if ( '1' == $mode_enabled ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '1' );
			} elseif ( '2' == $mode_enabled ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '2' );
			}

			update_post_meta( $attachment_id, 'quickwebp_data', $new_sizes );
			delete_post_meta( $attachment_id, 'quickwebp_has_error' );
		} else {
			update_post_meta( $attachment_id, 'quickwebp_has_error', '1' );
		}

		return $metadata;
	}

	/**
	 * Add data to the attachment
	 */
	public function add_data_to_attachment( $metadata, $attachment_id, $context ) {

		if ( $context != 'create' ) {
			return $metadata;
		}

		$settings = $this->get_settings();
		if ( $settings['quickwebp_settings_completion'] != '1' ) {
			return $metadata;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$original_name = isset( $_FILES['async-upload']['original_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['async-upload']['original_name'] ) ) : sanitize_text_field( $_FILES['async-upload']['name'] ?? '' );
		if ( ! empty( $original_name ) ) {
			$original_name = pathinfo( $original_name, PATHINFO_FILENAME );
			$post_arr      = array();

			$completion_options = is_array( $settings['quickwebp_settings_completion_options'] ) ? $settings['quickwebp_settings_completion_options'] : array();

			if ( in_array( 'title', $completion_options ) ) {
				$post_arr['post_title'] = $original_name;
			}
			
			if ( in_array( 'caption', $completion_options ) ) {
				$post_arr['post_excerpt'] = $original_name;
			}

			if ( in_array( 'alt', $completion_options ) ) {
				$post_arr['meta_input']['_wp_attachment_image_alt'] = $original_name;
			}
			
			if ( in_array( 'description', $completion_options ) ) {
				$post_arr['post_content'] = $original_name;
			}

			if ( ! empty( $post_arr ) ) {
				$post_arr['ID'] = $attachment_id;
				wp_update_post( $post_arr );
			}
		}

		return $metadata;
	}

	/**
	 * Change the default size of wp editor
	 */
	public function change_wp_max_size( $max_size, $imagesize ) {

		$settings      = $this->get_settings();
		$resize_active = $settings['quickwebp_settings_resize'];
		
		if ( $resize_active == '1' ) {

			$imagesize_width = isset($imagesize[0]) ? $imagesize[0] : 0;
			$resize_value    = $settings['quickwebp_settings_resize_value'];

			if ( $imagesize_width > $resize_value ) {
				$max_size = $resize_value;
			}
		}

		return $max_size;
	}

	/**
	 * Change the default quality of wp editor
	 */
	public function change_wp_quality( $default_quality ) {

		$settings     = $this->get_settings();
		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( $mode_enabled === '0' ) {
			return $default_quality;
		}

		return 100;
	}

	/**
	 * Optimize image through ajax
	 */
	public function image_optimization_ajax() {
		global $quickwebp_surecart_client;

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( __( 'You are not allowed to upload files.', 'quickwebp' ), 403 );
		}

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		// Get the file
		$file = count($_FILES) > 0 ? array_shift($_FILES) : array();
		if ( empty($file) ) {
			wp_send_json_error( __( 'No image uploaded, try again.', 'quickwebp' ) );
		}

		$settings   = $this->get_ajax_settings();
		$image_file = $this->file_is_image( $file, $settings );

		if ( ! $image_file ) {
			wp_send_json_error( __( 'No image uploaded, try again.', 'quickwebp' ) );
		}

		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( $mode_enabled === '0' ) {
			$image_file['new_size'] = $image_file['size'];
			$image_file['new_type'] = $image_file['type'];
			$this->return_ajax_data( $image_file );
		}

		$quality = $this->get_the_quality( $settings );
		$is_pro  = false;
		if ( $quickwebp_surecart_client ) {
			$is_pro = $quickwebp_surecart_client->license()->is_valid();
		}

		$image_file['new_size'] = $image_file['size'];
		$image_file['new_type'] = $image_file['type'];

		if ( '2' === $mode_enabled && $is_pro ) {
			$avif_image = $this->create_avif_image( $image_file['tmp_name'], $image_file['tmp_name'], $quality );
			if ( $avif_image ) {
				$image_file['new_size'] = filesize( $image_file['tmp_name'] );
				$image_file['new_type'] = 'image/avif';
			}
		}

		if ( '1' === $mode_enabled ) {
			$webp_image = $this->create_webp_image( $image_file['tmp_name'], $image_file['tmp_name'], $quality );
			if ( $webp_image ) {
				$image_file['new_size'] = filesize( $image_file['tmp_name'] );
				$image_file['new_type'] = 'image/webp';
			}
		}

		$this->return_ajax_data( $image_file );
	}

	/**
	 * Optimize a single media
	 */
	public function single_optimization_ajax() {

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'quickwebp_admin_attachment' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		// Sanitize data
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;

		if ( ! $attachment_id ) {
			wp_send_json_error( __( 'No attachment id.', 'quickwebp' ) );
		}

		$settings     = $this->get_settings();
		$mode_enabled = $settings['quickwebp_settings_conversion'];
		if ( '0' === $mode_enabled ) {
			wp_send_json_error( __( 'Choose an image format and save the settings.', 'quickwebp' ) );
		}

		$already_optimized = get_post_meta( $attachment_id, 'quickwebp_already_optimized', true );

		if ( '1' == $already_optimized && '1' == $mode_enabled ) {
			wp_send_json_error( __( 'Already optimized.', 'quickwebp' ) );
		}

		if ( '2' == $already_optimized && '2' == $mode_enabled ) {
			wp_send_json_error( __( 'Already optimized.', 'quickwebp' ) );
		}

		$post_mime_type = get_post_mime_type( $attachment_id );
		if ( ! in_array( $post_mime_type, $this->allowed_mime_types ) ) {
			wp_send_json_error( __( 'Not a valid image.', 'quickwebp' ) );
		}

		$sizes     = $this->get_media_files( $attachment_id );
		$new_sizes = array();

		foreach ( $sizes as $key => $size ) {
			$result = $this->optimize_local_file( $size );

			if ( $result ) {
				$new_sizes[$key] = $result;
			}
		}

		if ( ! empty( $new_sizes ) ) {

			$data = get_post_meta( $attachment_id, 'quickwebp_data', true );
			if ( ! empty( $data ) ) {
				$this->remove_related_files( $data );
			}

			if ( '1' == $mode_enabled ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '1' );
			} elseif ( '2' == $mode_enabled ) {
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '2' );
			}

			update_post_meta( $attachment_id, 'quickwebp_data', $new_sizes );
			delete_post_meta( $attachment_id, 'quickwebp_has_error' );
		} else {
			update_post_meta( $attachment_id, 'quickwebp_has_error', '1' );
			delete_post_meta( $attachment_id, 'quickwebp_already_optimized' );
			delete_post_meta( $attachment_id, 'quickwebp_data' );
		}

		$wp_media_extend = new Quickwebp_Wp_Media_Extends( $this->plugin_name, $this->version );
		$html            = $wp_media_extend->attachment_data( $new_sizes, $attachment_id );

		wp_send_json_success( $html );
	}

	/**
	 * Undo a single media optimization
	 */
	public function undo_single_optimization_ajax() {

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'quickwebp_admin_attachment' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		// Sanitize data
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;

		if ( ! $attachment_id ) {
			wp_send_json_error( __( 'No attachment id.', 'quickwebp' ) );
		}

		$data = get_post_meta( $attachment_id, 'quickwebp_data', true );
		if ( ! empty( $data ) ) {
			$this->remove_related_files( $data );
			delete_post_meta( $attachment_id, 'quickwebp_already_optimized' );
			delete_post_meta( $attachment_id, 'quickwebp_data' );

			$wp_media_extend = new Quickwebp_Wp_Media_Extends( $this->plugin_name, $this->version );
			$html            = $wp_media_extend->optimize_btn( $attachment_id );
			wp_send_json_success( $html );
		} else {
			wp_send_json_error( __( 'Not optimized.', 'quickwebp' ) );
		}
	}

	/**
	 * Trigger before delete attachment
	 */
	public function before_delete_attachment( $post_id ) {
		$data = get_post_meta( $post_id, 'quickwebp_data', true );
		if ( ! empty( $data ) ) {
			$this->remove_related_files( $data );
		}
	}

	/**
	 * Get the image data for the preview in the settings page
	 */
	public function get_image_data_for_preview_ajax() {

		// verify the nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', 'quickwebp' ) );
		}

		// Get the file
		$file = count($_FILES) > 0 ? array_shift($_FILES) : array();
		if ( empty($file) ) {
			wp_send_json_error( __( 'No image uploaded, try again.', 'quickwebp' ) );
		}

		$image_file = $this->file_is_image( $file );
		if ( ! $image_file ) {
			wp_send_json_error( __( 'No image uploaded, try again.', 'quickwebp' ) );
		}

		$preview_image_data = array(
			'name'       => $image_file['name'],
			'size'       => size_format( $image_file['size'], 2 ),
			'dimensions' => $image_file['width'] . ' x ' . $image_file['height'],
		);

		$conversions = array( '1', '2' );
		$qualities	 = array( 'low', 'medium', 'high', 'extra_high' );

		foreach ( $conversions as $conversion ) {
			foreach ( $qualities as $quality ) {

				$size_after = 0;

				if ( '2' == $conversion ) {
					$new_path      = $image_file['tmp_name'] . '-' . $quality . '.avif';
					$quality_value = $this->get_the_quality_values( $quality, $conversion );
					$avif_image    = $this->create_avif_image( $image_file['tmp_name'], $new_path, $quality_value );

					if ( $avif_image ) {
						$size_after = filesize( $new_path );
					}
				} elseif ( '1' == $conversion ) {
					$new_path      = $image_file['tmp_name'] . '-' . $quality . '.webp';
					$quality_value = $this->get_the_quality_values( $quality, $conversion );
					$webp_image    = $this->create_webp_image( $image_file['tmp_name'], $new_path, $quality_value );

					if ( $webp_image ) {
						$size_after = filesize( $new_path );
					}
				}

				$deference = $image_file['size'] - $size_after;
				$percent   = $deference / $image_file['size'] * 100;

				$preview_image_data[$conversion . '_' . $quality] = array(
					'size'         => size_format( $size_after, 2 ),
					'save'         => size_format( $deference, 2 ),
					'save_percent' => round( $percent ) . '%',
					'percent'      => round( 100 - $percent ) . '%',
				);
			}
		}
		
		wp_send_json_success( $preview_image_data );
	}

	/**
	 * Get the unoptimized media ids
	 */
	public function get_unoptimized_media_ids() {

		$settings           = $this->get_settings();
		$mode_enabled       = $settings['quickwebp_settings_conversion'];
		$ignore_same_format = is_array( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? $settings['quickwebp_settings_conversion_ignore_webp'] : array();

		$statuses = array(
			'inherit' => 'inherit',
			'private' => 'private',
		);
		$custom_statuses = get_post_stati( array( 'public' => true ) );
		unset( $custom_statuses['publish'] );
		if ( $custom_statuses ) {
			$statuses = array_merge( $statuses, $custom_statuses );
		}

		$allowed_mime_types = $this->allowed_mime_types;
		if ( in_array( 'checked', $ignore_same_format ) ) {
			if ( '1' == $mode_enabled ) {
				$webp_index = array_search( 'image/webp', $allowed_mime_types );
				unset( $allowed_mime_types[$webp_index] );
			} elseif ( '2' == $mode_enabled ) {
				$avif_index = array_search( 'image/avif', $allowed_mime_types );
				unset( $allowed_mime_types[$avif_index] );
			}
		}

		$meta_query_already_optimized = array(
			'relation' => 'OR',
			array(
				'key'     => 'quickwebp_already_optimized',
				'compare' => 'NOT EXISTS'
			),
		);

		if ( 'webp' == $mode_enabled ) {
			$meta_query_already_optimized[] = array(
				'key'     => 'quickwebp_already_optimized',
				'compare' => '!=',
				'value'   => '1',
			);
		} elseif ( 'avif' == $mode_enabled ) {
			$meta_query_already_optimized[] = array(
				'key'     => 'quickwebp_already_optimized',
				'compare' => '!=',
				'value'   => '2',
			);
		}

		$media_ids = get_posts( array(
			'post_type'      => 'attachment',
			'post_mime_type' => $allowed_mime_types,
			'post_status'    => array_keys( $statuses ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'quickwebp_has_error',
					'compare' => 'NOT EXISTS'
				),
				$meta_query_already_optimized,
			),
		) );

		return $media_ids;
	}

	/**
	 * Get the list of media files
	 */
	public function get_media_files( $media_id ) {
		$fullsize_path = get_attached_file( $media_id );

		if ( ! $fullsize_path ) {
			return array();
		}

		$media_data = wp_get_attachment_image_src( $media_id, 'full' );
		$file_type  = wp_check_filetype( $fullsize_path );

		$all_sizes  = [
			'full' => [
				'size'      => 'full',
				'path'      => $fullsize_path,
				'width'     => $media_data[1],
				'height'    => $media_data[2],
				'mime-type' => $file_type['type'],
				'disabled'  => false,
			],
		];

		$sizes = wp_get_attachment_metadata( $media_id, true );
		$sizes = ! empty( $sizes['sizes'] ) && is_array( $sizes['sizes'] ) ? $sizes['sizes'] : [];

		$dir_path = trailingslashit( dirname( $fullsize_path ) );

		foreach ( $sizes as $size => $size_data ) {
			$all_sizes[ $size ] = [
				'size'      => $size,
				'path'      => $dir_path . $size_data['file'],
				'width'     => $size_data['width'],
				'height'    => $size_data['height'],
				'mime-type' => $size_data['mime-type'],
				'disabled'  => false
			];
		}

		return $all_sizes;
	}

	/**
	 * Optimize a local file
	 */
	public function optimize_local_file( $size ) {
		global $quickwebp_surecart_client;

		if ( !is_file($size['path']) ) {
			return false;
		}

		$real_type = mime_content_type( $size['path']);
		if ( !in_array( $real_type, $this->allowed_mime_types ) ) {
			return false;
		}

		$settings = $this->get_settings();
		$quality  = $this->get_the_quality( $settings );
		$is_pro   = false;
		if ( $quickwebp_surecart_client ) {
			$is_pro = $quickwebp_surecart_client->license()->is_valid();
		}
		$mode_enabled = $settings['quickwebp_settings_conversion'];
		$size_before  = filesize( $size['path'] );
		$new_path     = $size['path'] . '.' . ( '2' === $mode_enabled && $is_pro ? 'avif' : 'webp' );
		$size_after   = 0;

		if ( '2' === $mode_enabled && $is_pro ) {
			$avif_image = $this->create_avif_image( $size['path'], $new_path, $quality );
			if ( $avif_image ) {
				$size_after = filesize( $new_path );
			}
		} elseif ( '1' === $mode_enabled ) {
			$webp_image = $this->create_webp_image( $size['path'], $new_path, $quality );
			if ( $webp_image ) {
				$size_after = filesize( $new_path );
			}
		}

		if ( ! $size_after ) {
			return false;
		}

		$deference = $size_before - $size_after;
		$percent   = $deference / $size_before * 100;

		return array(
			'success'        => 1,
			'original_size'  => $size_before,
			'optimized_size' => $size_after,
			'percent'        => round( $percent, 2 ),
			'path'			 => $new_path,
			'format'         => $mode_enabled,
		);
	}

	/**
	 * Remove the related files of an optimized attachment
	 */
	public function remove_related_files( $data ) {
		foreach ( $data as $value ) {
			$path = $value['path'] ?? '';

			if ( ! empty( $path ) && file_exists( $path ) ) {
				wp_delete_file( $path );
			}
		}
	}

	/**
	 * Get the optimization settings
	 */
	public function get_settings() {
		return array(
			'quickwebp_settings_conversion'				  => get_option('quickwebp_settings_conversion', quickwebp_settings_default('quickwebp_settings_conversion') ),
			'quickwebp_settings_conversion_quality'		  => get_option('quickwebp_settings_conversion_quality', quickwebp_settings_default('quickwebp_settings_conversion_quality') ),
			'quickwebp_settings_conversion_ignore_webp'	  => get_option('quickwebp_settings_conversion_ignore_webp', quickwebp_settings_default('quickwebp_settings_conversion_ignore_webp') ),
			'quickwebp_settings_conversion_save_original' => get_option('quickwebp_settings_conversion_save_original', quickwebp_settings_default('quickwebp_settings_conversion_save_original') ),
			'quickwebp_settings_resize'					  => get_option('quickwebp_settings_resize', quickwebp_settings_default('quickwebp_settings_resize') ),
			'quickwebp_settings_resize_value'			  => get_option('quickwebp_settings_resize_value', quickwebp_settings_default('quickwebp_settings_resize_value') ),
			'quickwebp_settings_completion'				  => get_option('quickwebp_settings_completion', quickwebp_settings_default('quickwebp_settings_completion') ),
			'quickwebp_settings_completion_options'		  => get_option('quickwebp_settings_completion_options', quickwebp_settings_default('quickwebp_settings_completion_options') ),
			'quickwebp_settings_cleanup'				  => get_option('quickwebp_settings_cleanup', quickwebp_settings_default('quickwebp_settings_cleanup') ),
		);
	}
	
	/**
	 * Get the optimization settings
	 */
	private function get_ajax_settings() {
		//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw_settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';
		$settings     = is_string( $raw_settings ) ? json_decode( $raw_settings, true ) : array();

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return array(
			'quickwebp_settings_conversion'				=> isset( $settings['quickwebp_settings_conversion'] ) ? sanitize_text_field( (string) $settings['quickwebp_settings_conversion'] ) : '0',
			'quickwebp_settings_conversion_quality'		=> isset( $settings['quickwebp_settings_conversion_quality'] ) ? sanitize_text_field( $settings['quickwebp_settings_conversion_quality'] ) : 'high',
			'quickwebp_settings_conversion_ignore_webp'	=> isset( $settings['quickwebp_settings_conversion_ignore_webp'] ) && is_array( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? array_map( 'sanitize_text_field', $settings['quickwebp_settings_conversion_ignore_webp'] ) : array(),
			'quickwebp_settings_resize'					=> isset( $settings['quickwebp_settings_resize'] ) ? sanitize_text_field( (string) $settings['quickwebp_settings_resize'] ) : '',
			'quickwebp_settings_resize_value'			=> isset( $settings['quickwebp_settings_resize_value'] ) ? absint( $settings['quickwebp_settings_resize_value'] ) : 0,
			'quickwebp_settings_completion'				=> isset( $settings['quickwebp_settings_completion'] ) ? sanitize_text_field( (string) $settings['quickwebp_settings_completion'] ) : '',
			'quickwebp_settings_completion_options'		=> isset( $settings['quickwebp_settings_completion_options'] ) && is_array( $settings['quickwebp_settings_completion_options'] ) ? array_map( 'sanitize_text_field', $settings['quickwebp_settings_completion_options'] ) : array(),
			'quickwebp_settings_cleanup'				=> isset( $settings['quickwebp_settings_cleanup'] ) ? sanitize_text_field( (string) $settings['quickwebp_settings_cleanup'] ) : '',
		);
	}

	/**
	 * Check if the file is an image
	 */
	private function file_is_image( $file, $settings = array() ) {

		$file_name		= isset($file['name']) 		? $file['name'] 	: '';
		$file_type		= isset($file['type']) 		? $file['type'] 	: '';
		$file_tmp_name	= isset($file['tmp_name'])	? $file['tmp_name']	: '';
		$file_error		= isset($file['error']) 	? $file['error'] 	: '';
		$file_size		= isset($file['size']) 		? $file['size'] 	: '';

		if ( empty($file_tmp_name) ) {
			return false;
		}

		$allowed_mime_types	= apply_filters( 'quickwebp_mime_types_allowed', $this->allowed_mime_types );
		$is_image			= wp_getimagesize($file_tmp_name);
		$mime_type 			= wp_get_image_mime($file_tmp_name);
		if ( ! $is_image || ! in_array( $mime_type, $allowed_mime_types ) ) {
			return false;
		}

		$name = $file_name;

		if ( isset($settings['quickwebp_settings_cleanup']) && $settings['quickwebp_settings_cleanup'] == '1' ) {
			$name =  quickwebp_sanitize_name($file_name);
		}

		return array(
			'original_name'	=> $file_name,
			'name'			=> $name,
			'type'			=> $file_type,
			'tmp_name'		=> $file_tmp_name,
			'error'			=> $file_error,
			'size'			=> $file_size,
			'width'			=> $is_image[0],
			'height'		=> $is_image[1],
		);
	}

	/**
	 * Return ajax data
	 */
	private function return_ajax_data( $data ) {
		$return = array(
			'original_name'	=> $data['original_name'],
			'size'			=> $data['size'],
			'type'			=> $this->type_from_mime_type( $data['type'] ),
			'mime_type'		=> $data['type'],
			'image'			=> 'data:'.$data['new_type'].';base64,' . base64_encode( file_get_contents( $data['tmp_name'] ) ),
			'name'			=> $data['name'],
			'new_size'		=> $data['new_size'],
			'new_type'		=> $this->type_from_mime_type( $data['new_type'] ),
			'new_mime_type'	=> $data['new_type']
		);

		wp_send_json_success( $return );
	}

	/**
	 * Get the type from the mime type
	 */
	private function type_from_mime_type( $mime_type ) {
		$array = array(
			'image/jpeg' => 'JPEG',
			'image/png'  => 'PNG',
			'image/webp' => 'WebP',
			'image/avif' => 'AVIF',
		);

		return $array[$mime_type] ?? '';
	}

	/**
	 * Get the quality
	 */
	private function get_the_quality( $settings ) {
		$mode_enabled = $settings['quickwebp_settings_conversion'];
		$quality      = $settings['quickwebp_settings_conversion_quality'];

		return $this->get_the_quality_values( $quality, $mode_enabled );
	}

	/**
	 * Get the quality values for avif and webp
	 */
	private function get_the_quality_values( $quality, $mode_enabled ) {
		$result = 50;

		switch ( $quality ) {
			case 'low':
				$result = 50;
				if ( '2' === $mode_enabled ) {
					$result = 30;
				}
			break;
			case 'medium':
				$result = 60;
				if ( '2' === $mode_enabled ) {
					$result = 40;
				}
			break;
			case 'high':
				$result = 75;
				if ( '2' === $mode_enabled ) {
					$result = 50;
				}
			break;
			case 'extra_high':
				$result = 90;
				if ( '2' === $mode_enabled ) {
					$result = 70;
				}
			break;
		}

		return $result;
	}

	/**
	 * Get the extension matching an image mime type.
	 *
	 * @param string $mime_type Image mime type.
	 *
	 * @return string
	 */
	private function get_extension_from_mime_type( $mime_type ) {
		$extensions = array(
			'image/avif' => 'avif',
			'image/webp' => 'webp',
			'image/jpeg' => 'jpg',
			'image/png'  => 'png',
		);

		return $extensions[ $mime_type ] ?? '';
	}

	/**
	 * Create an image using the WordPress image editor abstraction.
	 *
	 * @param string $file_path        Path to the source image.
	 * @param string $output_path      Path where the converted image should be written.
	 * @param int    $quality          Compression quality.
	 * @param string $target_mime_type Output mime type.
	 *
	 * @return bool
	 */
	private function create_image_with_wp_editor( $file_path, $output_path, $quality, $target_mime_type ) {
		$editor = wp_get_image_editor( $file_path );

		if ( is_wp_error( $editor ) ) {
			return false;
		}

		$extension = $this->get_extension_from_mime_type( $target_mime_type );
		if ( empty( $extension ) ) {
			return false;
		}

		$save_path = $output_path;
		if ( pathinfo( $output_path, PATHINFO_EXTENSION ) !== $extension ) {
			$save_path = $output_path . '.' . $extension;
		}

		$editor->set_quality( $quality );
		$result = $editor->save( $save_path, $target_mime_type );

		if ( is_wp_error( $result ) || empty( $result['path'] ) || ! file_exists( $result['path'] ) ) {
			return false;
		}

		if ( $result['path'] !== $output_path ) {
			$file_contents = file_get_contents( $result['path'] );

			if ( false === $file_contents || false === file_put_contents( $output_path, $file_contents ) ) {
				return false;
			}

			wp_delete_file( $result['path'] );
		}

		return true;
	}

	/**
	 * Create a GD image resource when the required loader is available.
	 *
	 * @param string $file_path Path to the source image.
	 * @param string $mime_type Source mime type.
	 *
	 * @return GdImage|resource|false
	 */
	private function create_gd_image_resource( $file_path, $mime_type ) {
		$loaders = array(
			'image/avif' => 'imagecreatefromavif',
			'image/webp' => 'imagecreatefromwebp',
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/png'  => 'imagecreatefrompng',
		);

		if ( empty( $loaders[ $mime_type ] ) ) {
			return false;
		}

		$loader = $loaders[ $mime_type ];

		if ( ! function_exists( $loader ) ) {
			return false;
		}

		return $loader( $file_path );
	}

	/**
	 * Create the avif image
	 */
	private function create_avif_image( $file_path, $output_path, $quality ) {
		if ( $this->create_image_with_wp_editor( $file_path, $output_path, $quality, 'image/avif' ) ) {
			return true;
		}

		$image     = false;
		$mime_type = wp_get_image_mime( $file_path );
		$image     = $this->create_gd_image_resource( $file_path, $mime_type );

		if ( ! $image ) {
			return false;
		}

		// Handle EXIF orientation for proper image rotation
		$exif_data = @exif_read_data( $file_path );
		if ( ! empty( $exif_data['Orientation'] ) ) {
			$orientation = (int) $exif_data['Orientation'];
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$orientation = apply_filters( 'wp_image_maybe_exif_rotate', $orientation, $file_path );
			if ( $orientation && 1 !== $orientation ) {
				switch ( $orientation ) {
					case 2:
						// Flip horizontally.
						imageflip( $image, IMG_FLIP_HORIZONTAL );
						break;
					case 3:
						// Rotate 180 degrees.
						$image = imagerotate( $image, 180, 0 );
						break;
					case 4:
						// Flip vertically.
						imageflip( $image, IMG_FLIP_VERTICAL );
						break;
					case 5:
						// Rotate 90 degrees counter-clockwise and flip vertically.
						$image = imagerotate( $image, -90, 0 );
						imageflip( $image, IMG_FLIP_VERTICAL );
						break;
					case 6:
						// Rotate 90 degrees clockwise (270 counter-clockwise).
						$image = imagerotate( $image, -90, 0 );
						break;
					case 7:
						// Rotate 90 degrees counter-clockwise and flip horizontally.
						$image = imagerotate( $image, 90, 0 );
						imageflip( $image, IMG_FLIP_HORIZONTAL );
						break;
					case 8:
						// Rotate 90 degrees counter-clockwise.
						$image = imagerotate( $image, 90, 0 );
						break;
				}
			}
		}
		
		if ( ! imageistruecolor( $image ) ) {
			$truecolor = imagecreatetruecolor( imagesx( $image ), imagesy( $image ) );
	
			if ( $mime_type === 'image/png' ) {
				imagealphablending( $truecolor, false );
				imagesavealpha( $truecolor, true );
				$transparent = imagecolorallocatealpha( $truecolor, 0, 0, 0, 127 );
				imagefilledrectangle( $truecolor, 0, 0, imagesx( $image ), imagesy( $image ), $transparent );
			}
	
			imagecopy( $truecolor, $image, 0, 0, 0, 0, imagesx( $image ), imagesy( $image ) );
			$image = $truecolor;
		}

		$avif_image = imageavif( $image, $output_path, $quality );
		imagedestroy( $image );
		if ( ! $avif_image ) {
			return false;
		}

		return $avif_image;
	}

	/**
	 * Create the webp image
	 */
	private function create_webp_image( $file_path, $output_path, $quality ) {
		if ( $this->create_image_with_wp_editor( $file_path, $output_path, $quality, 'image/webp' ) ) {
			return true;
		}

		$image     = false;
		$mime_type = wp_get_image_mime( $file_path );
		$image     = $this->create_gd_image_resource( $file_path, $mime_type );

		if ( ! $image ) {
			return false;
		}

		// Handle EXIF orientation for proper image rotation
		$exif_data = @exif_read_data( $file_path );
		if ( ! empty( $exif_data['Orientation'] ) ) {
			$orientation = (int) $exif_data['Orientation'];
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$orientation = apply_filters( 'wp_image_maybe_exif_rotate', $orientation, $file_path );
			if ( $orientation && 1 !== $orientation ) {
				switch ( $orientation ) {
					case 2:
						// Flip horizontally.
						imageflip( $image, IMG_FLIP_HORIZONTAL );
						break;
					case 3:
						// Rotate 180 degrees.
						$image = imagerotate( $image, 180, 0 );
						break;
					case 4:
						// Flip vertically.
						imageflip( $image, IMG_FLIP_VERTICAL );
						break;
					case 5:
						// Rotate 90 degrees counter-clockwise and flip vertically.
						$image = imagerotate( $image, -90, 0 );
						imageflip( $image, IMG_FLIP_VERTICAL );
						break;
					case 6:
						// Rotate 90 degrees clockwise (270 counter-clockwise).
						$image = imagerotate( $image, -90, 0 );
						break;
					case 7:
						// Rotate 90 degrees counter-clockwise and flip horizontally.
						$image = imagerotate( $image, 90, 0 );
						imageflip( $image, IMG_FLIP_HORIZONTAL );
						break;
					case 8:
						// Rotate 90 degrees counter-clockwise.
						$image = imagerotate( $image, 90, 0 );
						break;
				}
			}
		}
		
		if ( ! imageistruecolor( $image ) ) {
			$truecolor = imagecreatetruecolor( imagesx( $image ), imagesy( $image ) );
	
			if ( $mime_type === 'image/png' ) {
				imagealphablending( $truecolor, false );
				imagesavealpha( $truecolor, true );
				$transparent = imagecolorallocatealpha( $truecolor, 0, 0, 0, 127 );
				imagefilledrectangle( $truecolor, 0, 0, imagesx( $image ), imagesy( $image ), $transparent );
			}
	
			imagecopy( $truecolor, $image, 0, 0, 0, 0, imagesx( $image ), imagesy( $image ) );
			$image = $truecolor;
		}

		$webp_image = imagewebp( $image, $output_path, $quality );
		imagedestroy( $image );
		if ( ! $webp_image ) {
			return false;
		}

		return $webp_image;
	}
}
