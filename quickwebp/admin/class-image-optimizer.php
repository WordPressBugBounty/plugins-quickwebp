<?php

use Intervention\Image\ImageManager;

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

		$this->plugin_name			= $plugin_name;
		$this->version				= $version;
		$this->allowed_mime_types	= array( 'image/jpeg', 'image/png', 'image/webp' );

	}

	/**
	 * Optimize an image added in wp_media
	 */
	public function image_optimizition( $file ) {

		$settings	= $this->get_settings();

		$image_file = $this->file_is_image( $file, $settings );
		if ( ! $image_file ) {
			return $file;
		}

		if ( $settings['quickwebp_settings_conversion'] != '1' ) {
			return $image_file;
		}

		$save_original	= is_array( $settings['quickwebp_settings_conversion_save_original'] ) ? $settings['quickwebp_settings_conversion_save_original'] : array();
		if ( in_array( 'checked', $save_original ) ) {
			return $image_file;
		}

		$extension_to_use = $this->image_extension_loaded( $settings );
		if ( ! $extension_to_use ) {
			return $image_file;
		}

		$manager	= new ImageManager(array('driver'=>$extension_to_use));
		$image		= $manager->make($image_file['tmp_name']);
		$quality 	= $this->get_quality( $image_file['tmp_name'], $settings );

		$image->sharpen($settings['quickwebp_settings_conversion_sharpen']);
		$image->save( $image_file['tmp_name'], $quality, 'webp' );

		$size_after 	= $image->filesize();
		$mime 			= $image->mime();

		$image_file['size'] = $size_after;
		$image_file['type'] = $mime;
		
		return $image_file;
	}

	/**
	 * Get the package used by the server
	 * 
	 */
	public function image_extension_loaded( $settings ) {

		$accepted_librarys	= array( 'gd', 'imagick' );
		$library_to_use		= $settings['quickwebp_settings_library'];

		if ( in_array( $library_to_use, $accepted_librarys ) ) {

			return $library_to_use;
		}

		return false;
	}

	/**
	 * Check if the file is an image
	 * 
	 */
	public function file_is_image( $file, $settings ) {

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

		if ( $settings['quickwebp_settings_cleanup'] == '1' ) {
			$name =  quickwebp_sanitize_name($file_name);
		}

		return array(
			'original_name'	=> $file_name,
			'name'			=> $name,
			'type'			=> $file_type,
			'tmp_name'		=> $file_tmp_name,
			'error'			=> $file_error,
			'size'			=> $file_size
		);
	}

	/**
	 * Get the optimization settings
	 */
	public function get_settings() {

		return array(
			'quickwebp_settings_conversion'					=> get_option('quickwebp_settings_conversion', quickwebp_settings_default('quickwebp_settings_conversion') ),
			'quickwebp_settings_conversion_quality'			=> get_option('quickwebp_settings_conversion_quality', quickwebp_settings_default('quickwebp_settings_conversion_quality') ),
			'quickwebp_settings_conversion_sharpen'			=> get_option('quickwebp_settings_conversion_sharpen', quickwebp_settings_default('quickwebp_settings_conversion_sharpen') ),
			'quickwebp_settings_conversion_ignore_webp'		=> get_option('quickwebp_settings_conversion_ignore_webp', quickwebp_settings_default('quickwebp_settings_conversion_ignore_webp') ),
			'quickwebp_settings_conversion_save_original'	=> get_option('quickwebp_settings_conversion_save_original', quickwebp_settings_default('quickwebp_settings_conversion_save_original') ),
			'quickwebp_settings_resize'						=> get_option('quickwebp_settings_resize', quickwebp_settings_default('quickwebp_settings_resize') ),
			'quickwebp_settings_resize_value'				=> get_option('quickwebp_settings_resize_value', quickwebp_settings_default('quickwebp_settings_resize_value') ),
			'quickwebp_settings_completion'					=> get_option('quickwebp_settings_completion', quickwebp_settings_default('quickwebp_settings_completion') ),
			'quickwebp_settings_completion_options'			=> get_option('quickwebp_settings_completion_options', quickwebp_settings_default('quickwebp_settings_completion_options') ),
			'quickwebp_settings_cleanup'					=> get_option('quickwebp_settings_cleanup', quickwebp_settings_default('quickwebp_settings_cleanup') ),
			'quickwebp_settings_library'					=> get_option( 'quickwebp_settings_library', quickwebp_settings_default('quickwebp_settings_library') )
		);
	}
	
	/**
	 * Get the optimization settings
	 */
	public function get_ajax_settings() {

		$settings	= isset($_POST['settings']) ? sanitize_text_field($_POST['settings']) : array();
		$settings 	= json_decode( stripslashes( $settings ), true );

		return array(
			'quickwebp_settings_conversion'				=> isset( $settings['quickwebp_settings_conversion'] ) ? $settings['quickwebp_settings_conversion'] : '',
			'quickwebp_settings_conversion_quality'		=> isset( $settings['quickwebp_settings_conversion_quality'] ) ? $settings['quickwebp_settings_conversion_quality'] : '',
			'quickwebp_settings_conversion_sharpen'		=> isset( $settings['quickwebp_settings_conversion_sharpen'] ) ? $settings['quickwebp_settings_conversion_sharpen'] : '',
			'quickwebp_settings_conversion_ignore_webp'	=> isset( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? $settings['quickwebp_settings_conversion_ignore_webp'] : array(),
			'quickwebp_settings_resize'					=> isset( $settings['quickwebp_settings_resize'] ) ? $settings['quickwebp_settings_resize'] : '',
			'quickwebp_settings_resize_value'			=> isset( $settings['quickwebp_settings_resize_value'] ) ? $settings['quickwebp_settings_resize_value'] : '',
			'quickwebp_settings_completion'				=> isset( $settings['quickwebp_settings_completion'] ) ? $settings['quickwebp_settings_completion'] : '',
			'quickwebp_settings_completion_options'		=> isset( $settings['quickwebp_settings_completion_options'] ) ? $settings['quickwebp_settings_completion_options'] : array(),
			'quickwebp_settings_cleanup'				=> isset( $settings['quickwebp_settings_cleanup'] ) ? $settings['quickwebp_settings_cleanup'] : '',
			'quickwebp_settings_library'				=> isset( $settings['quickwebp_settings_library'] ) ? $settings['quickwebp_settings_library'] : ''
		);
	}

	/**
	 * Add data to the attachment
	 */
	public function add_data_to_attachment( $metadata, $attachment_id, $context ) {

		$settings = $this->get_settings();
		if ( $settings['quickwebp_settings_completion'] != '1' ) {
			return $metadata;
		}

		if ( $context != 'create' ) {
			return $metadata;
		}

		if ( isset($_FILES['async-upload']['original_name']) ) {

			$original_name = sanitize_text_field( $_FILES['async-upload']['original_name'] );
			$original_name = pathinfo( $original_name, PATHINFO_FILENAME );

			$post_arr = array(
				'ID'			=> $attachment_id,
				'meta_input'	=> array()
			);

			$completion_options = is_array($settings['quickwebp_settings_completion_options']) ? $settings['quickwebp_settings_completion_options'] : array();

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

			wp_update_post( $post_arr );
		}

		$save_original	= is_array( $settings['quickwebp_settings_conversion_save_original'] ) ? $settings['quickwebp_settings_conversion_save_original'] : array();
		if ( in_array( 'checked', $save_original ) ) {
			$sizes		= $this->get_media_files( $attachment_id );
			$new_sizes 	= array();
			
			foreach ( $sizes as $key => $size ) {
				$result = $this->optimize_local_file( $size );
	
				if ( $result ) {
					$new_sizes[$key] = $result;
				}
			}
	
			if ( ! empty( $new_sizes ) ) {
	
				update_post_meta( $attachment_id, 'quickwebp_already_optimized', '1' );
				update_post_meta( $attachment_id, 'quickwebp_data', $new_sizes );
				delete_post_meta( $attachment_id, 'quickwebp_has_error' );
			} else {
				update_post_meta( $attachment_id, 'quickwebp_has_error', '1' );
			}
		}

		return $metadata;
	}

	/**
	 * Get the quality
	 */
	public function get_quality( $file, $settings ) {

		$ignore_webp	= is_array( $settings['quickwebp_settings_conversion_ignore_webp'] ) ? $settings['quickwebp_settings_conversion_ignore_webp'] : array();
		$quality		= $settings['quickwebp_settings_conversion_quality'];
		$mime_type		= wp_get_image_mime($file);

		switch ( $mime_type ) {

			case 'image/webp':
				if ( in_array( 'checked', $ignore_webp ) ){
					$quality = 100;
				}
			break;
		}

		return $quality;
	}

	/**
	 * Change the default size of wp editor
	 */
	public function change_wp_max_size( $max_size, $imagesize, $file, $attachment_id ) {

		$settings		= $this->get_settings();
		$resize_active	= $settings['quickwebp_settings_resize'];
		$resize_value	= $settings['quickwebp_settings_resize_value'];

		if ( $resize_active == '1' ) {

			$max_size = $resize_value;
		}

		return $max_size;
	}

	/**
	 * Change the default quality of wp editor
	 */
	public function change_wp_quality( $default_quality, $mime_type ) {

		$default_quality = 100;

		return $default_quality;
	}

	/**
	 * Optimize image through ajax
	 */
	public function image_optimizition_ajax() {

		// verify the nonce.
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		if( !wp_verify_nonce( $nonce, 'image_optimize_nonce' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		// Get settings
		$settings	= $this->get_ajax_settings();

		// Get the file
		$file = count($_FILES) > 0 ? array_shift($_FILES) : array();
		if ( empty($file) ) {
			wp_send_json_error( __( 'No image uploaded, try again.', QUICKWEBP_TEXT_DOMAIN ) );
		}
		

		$image_file = $this->file_is_image( $file, $settings );
		if ( ! $image_file ) {
			wp_send_json_error( __( 'No image uploaded, try again.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		if ( $settings['quickwebp_settings_conversion'] != '1' ) {
			
			$image_file['new_size'] = $image_file['size'];
			$image_file['new_type'] = $image_file['type'];
			$this->return_ajax_data( $image_file );
		}

		$extension_to_use = $this->image_extension_loaded( $settings );
		if ( ! $extension_to_use ) {

			$image_file['new_size'] = $image_file['size'];
			$image_file['new_type'] = $image_file['type'];
			$this->return_ajax_data( $image_file );
		}

		$manager	= new ImageManager(array('driver'=>$extension_to_use));
		$image		= $manager->make($image_file['tmp_name']);
		$quality 	= $this->get_quality( $image_file['tmp_name'], $settings );

		$image->sharpen( $settings['quickwebp_settings_conversion_sharpen'] );
		$image->save( $image_file['tmp_name'], $quality, 'webp' );

		$image_file['new_size'] = $image->filesize();
		$image_file['new_type'] = $image->mime();
		$this->return_ajax_data( $image_file );
	}

	/**
	 * Return ajax data
	 */
	public function return_ajax_data( $data ) {

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
	public function type_from_mime_type( $mime_type ) {

		$array = array(
			'image/jpeg' 	=> 'JPEG',
			'image/png'		=> 'PNG',
			'image/webp'	=> 'WebP'
		);

		return $array[$mime_type] ?? '';
	}

	/**
	 * Get the unoptimized media ids
	 */
	public function get_unoptimized_media_ids() {

		$statuses = array(
			'inherit' => 'inherit',
			'private' => 'private',
		);
		$custom_statuses = get_post_stati( array( 'public' => true ) );
		unset( $custom_statuses['publish'] );
		if ( $custom_statuses ) {
			$statuses = array_merge( $statuses, $custom_statuses );
		}

		$mime_types	= $this->allowed_mime_types;
		$index 		= array_search( 'image/webp', $mime_types );
		unset( $mime_types[$index] );

		$media_ids = get_posts( array(
			'post_type'      => 'attachment',
			'post_mime_type' => $mime_types,
			'post_status'    => array_keys( $statuses ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'quickwebp_has_error',
					'compare' => 'NOT EXISTS'
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'quickwebp_already_optimized',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key'     => 'quickwebp_already_optimized',
						'compare' => '=',
						'value'   => '0'
					)
				),
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

		$settings	= $this->get_settings();

		$extension_to_use = $this->image_extension_loaded( $settings );
		if ( ! $extension_to_use ) {
			return false;
		}

		if ( !is_file($size['path']) ) {
			return false;
		}

		$real_type = mime_content_type( $size['path']);
		if ( !in_array( $real_type, $this->allowed_mime_types ) ) {
			return false;
		}

		try {
			$size_before	= filesize( $size['path'] );
			$manager		= new ImageManager(array('driver'=>$extension_to_use));
			$image			= $manager->make($size['path']);
			$quality 		= $this->get_quality( $size['path'], $settings );
			$webp_path		= $size['path'].'.webp';
	
			$image->sharpen($settings['quickwebp_settings_conversion_sharpen']);
			$image->save( $webp_path, $quality, 'webp' );
			$size_after = $image->filesize();
			$image->destroy();
	
			$deference	= $size_before - $size_after;
			$percent	= $deference / $size_before * 100;
	
			return array(
				'success'			=> 1,
				'original_size'		=> $size_before,
				'optimized_size'	=> $size_after,
				'percent' 			=> round( $percent, 2 ),
				'path'				=> $webp_path
			);
		} catch (\Throwable $th) {
			return false;
		}
	}

	/**
	 * Optimize a single media
	 */
	public function single_optimizition_ajax() {

		// verify the nonce.
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		if( !wp_verify_nonce( $nonce, 'quickwebp_admin_attachment' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		// Sanitize data
		$attachment_id	= isset( $_POST['attachment_id'] ) ? sanitize_text_field( $_POST['attachment_id'] ) : false;

		if ( ! $attachment_id ) {
			wp_send_json_error( __( 'No attachment id.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		$already_optimized = get_post_meta( $attachment_id, 'quickwebp_already_optimized', true );
		if ( $already_optimized === '1' ) {
			wp_send_json_error( __( 'Already optimized.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		// check the mime type
		$mime_types	= $this->allowed_mime_types;
		$index 		= array_search( 'image/webp', $mime_types );
		unset( $mime_types[$index] );
		$mime_type 	= get_post_mime_type( $attachment_id );

		if ( ! in_array( $mime_type, $mime_types ) ) {
			wp_send_json_error( __( 'Not a valid image.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		$sizes	= $this->get_media_files( $attachment_id );
		$new_sizes  = array();

		foreach ( $sizes as $key => $size ) {

			$result = $this->optimize_local_file( $size );

			if ( $result ) {
				$new_sizes[$key] = $result;
			}
		}

		if ( ! empty( $new_sizes ) ) {
			update_post_meta( $attachment_id, 'quickwebp_already_optimized', '1' );
			update_post_meta( $attachment_id, 'quickwebp_data', $new_sizes );
			delete_post_meta( $attachment_id, 'quickwebp_has_error' );
		} else {
			update_post_meta( $attachment_id, 'quickwebp_has_error', '1' );
		}

		$wp_media_extend = new Quickwebp_Wp_Media_Extends( $this->plugin_name, $this->version );
		$html = $wp_media_extend->attachment_data( $new_sizes, $attachment_id );

		wp_send_json_success( $html );
	}

	/**
	 * Undo a single media optimization
	 */
	public function undo_single_optimizition_ajax() {

		// verify the nonce.
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		if( !wp_verify_nonce( $nonce, 'quickwebp_admin_attachment' ) ) {
			wp_send_json_error( __( 'Refresh the page and try again.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		// Sanitize data
		$attachment_id	= isset( $_POST['attachment_id'] ) ? sanitize_text_field( $_POST['attachment_id'] ) : false;

		if ( ! $attachment_id ) {
			wp_send_json_error( __( 'No attachment id.', QUICKWEBP_TEXT_DOMAIN ) );
		}

		$already_optimized = get_post_meta( $attachment_id, 'quickwebp_already_optimized', true );
		if ( $already_optimized === '1' ) {

			$this->remove_related_files( $attachment_id );
			delete_post_meta( $attachment_id, 'quickwebp_already_optimized' );
			delete_post_meta( $attachment_id, 'quickwebp_data' );

			$wp_media_extend = new Quickwebp_Wp_Media_Extends( $this->plugin_name, $this->version );
			$html = $wp_media_extend->optimize_btn( $attachment_id );

			wp_send_json_success( $html );

		} else {
			wp_send_json_error( __( 'Not optimized.', QUICKWEBP_TEXT_DOMAIN ) );
		}

	}

	/**
	 * Remove the related files of an optimized attachment
	 */
	public function remove_related_files( $id ) {

		$data = get_post_meta( $id, 'quickwebp_data', true );
			
		if ( ! empty( $data ) ) {
			
			foreach ( $data as $key => $value ) {
			
				$path = $value['path'] ?? '';

				if ( !empty($path) && file_exists( $path ) ) {
					wp_delete_file($path);
				}
			}
		}

	}

	/**
	 * Trigger before delete attachment
	 */
	public function before_delete_attachment( $post_id, $post ) {

		$already_optimized = get_post_meta( $post_id, 'quickwebp_already_optimized', true );
		if ( $already_optimized === '1' ) {

			$this->remove_related_files( $post_id );
		}

	}

}