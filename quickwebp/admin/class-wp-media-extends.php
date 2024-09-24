<?php
/**
 * The class for wp_media extends
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Wp_Media_Extends {

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

	public function enqueue_scripts(){
		global $pagenow;
		
		if ( get_option('quickwebp_settings_paste_image', quickwebp_settings_default('quickwebp_settings_paste_image')) == '1' ){

			$wp_media_extends_assets = include( QUICKWEBP_PLUGIN_PATH . 'public/assets/build/wp-media-extends.asset.php' );
			wp_enqueue_style( 'quickwebp-wp-media-extends', QUICKWEBP_PLUGIN_URL . 'public/assets/build/wp-media-extends.css', array(), $wp_media_extends_assets['version'], 'all' );
			wp_enqueue_script( 'quickwebp-wp-media-extends', QUICKWEBP_PLUGIN_URL . 'public/assets/build/wp-media-extends.js', $wp_media_extends_assets['dependencies'], $wp_media_extends_assets['version'], true );
			wp_localize_script( 'quickwebp-wp-media-extends', 'quickwebp_wp_media_extends', array(
				'is_upload_page' 			=> $pagenow ? $pagenow === 'upload.php' : false,
				'nonce' 					=> wp_create_nonce( 'media-form' ),
				'tmpl_tab_media_paste'		=> file_get_contents( QUICKWEBP_PLUGIN_PATH . 'admin/templates/tab-media-paste.php' ),
				'tmpl_button_media_paste'	=> file_get_contents( QUICKWEBP_PLUGIN_PATH . 'admin/templates/button-media-paste.php' ),
				'l10n'						=> array(
					'prompt_text'	=> __( 'Please enter the name of your image', QUICKWEBP_TEXT_DOMAIN ),
					'button_text'	=> __( 'Quickwebp paste image', QUICKWEBP_TEXT_DOMAIN ),
					'click_paste'	=> __( 'Click here, and paste your image.', QUICKWEBP_TEXT_DOMAIN )
				)
			));
		}

		$admin_attachment_assets = include( QUICKWEBP_PLUGIN_PATH . 'public/assets/build/admin-attachment.asset.php' );
		wp_enqueue_style( 'quickwebp-admin-attachment', QUICKWEBP_PLUGIN_URL . 'public/assets/build/admin-attachment.css', array(), $admin_attachment_assets['version'], 'all' );
		wp_enqueue_script( 'quickwebp-admin-attachment', QUICKWEBP_PLUGIN_URL . 'public/assets/build/admin-attachment.js', $admin_attachment_assets['dependencies'], $admin_attachment_assets['version'], true );
		wp_localize_script( 'quickwebp-admin-attachment', 'QUICKWEBP_ADMIN_ATTACHMENT', array(
			'nonce' 	=> wp_create_nonce( 'quickwebp_admin_attachment' ),
			'ajaxUrl'	=> admin_url( 'admin-ajax.php' )
		));
	}

	/**
	 * Add "Quickwebp" column in the Media Uploader
	 */
	public function add_attachment_fields_to_edit( $form_fields, $post ) {
		global $pagenow;

		if ( 'post.php' === $pagenow ) {
			return $form_fields;
		}

		if ( ! $this->valid_mimetype( $post->ID ) ) {
			return $form_fields;
		}

		$data = get_post_meta( $post->ID, 'quickwebp_data', true );

		if ( ! is_array( $data ) ) {

			$html = $this->optimize_btn( $post->ID );

		} else {

			$html = $this->attachment_data( $data, $post->ID );
		}

		$form_fields['quickwebp'] = array(
			'label' 		=> 'Quickwebp',
			'input' 		=> 'html',
			'html' 			=> $html,
			'show_in_edit'	=> true,
			'show_in_modal'	=> true
		);

		return $form_fields;
	}

	/**
	 * Add "quickwebp" column in upload.php
	 */
	public function add_media_columns( $columns ) {

		$columns['quickwebp'] = __( 'Quickwebp', QUICKWEBP_TEXT_DOMAIN );

		return $columns;
	}

	/**
	 * Add "Quickwebp" column in the Media list table
	 */
	public function add_media_custom_column( $column_name, $attachment_id ) {

		if ( 'quickwebp' !== $column_name ) {
			return;
		}

		if ( ! $this->valid_mimetype( $attachment_id ) ) {
			echo __( 'Image type not supported', QUICKWEBP_TEXT_DOMAIN );
			return;
		}

		$data = get_post_meta( $attachment_id, 'quickwebp_data', true );

		if ( ! is_array( $data ) ) {

			echo $this->optimize_btn( $attachment_id );

		} else {

			echo $this->attachment_data( $data, $attachment_id );
		}
	}

	/**
	 * Add a "Optimize" button or the Imagify optimization data in the attachment submit area.
	 */
	public function add_attachment_submitbox_misc_actions() {
		global $post;

		if ( ! $this->valid_mimetype( $post->ID ) ) {
			return;
		}

		$data = get_post_meta( $post->ID, 'quickwebp_data', true );

		if ( ! is_array( $data ) ) {

			echo '<table><tr><td>' . $this->optimize_btn( $post->ID ) . '</td></tr></table>';

		} else {

			echo '<table><tr><td>' . $this->attachment_data( $data, $post->ID ) . '</td></tr></table>';
		}

	}

	/**
	 * Get all data to display for a specific media
	 */
	public function attachment_data( $data, $attachment_id ) {

		$full_image_data = $data['full'] ?? array();

		if ( ! empty( $full_image_data ) ) {

			ob_start();

				?>
					<div>
						<strong><?php _e( 'Original Image: ', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
						<span><?php echo round( $full_image_data['original_size'] / 1024, 2 ) . 'KB'; ?></span>
					</div>
					<div>
						<strong><?php _e( 'Webp: ', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
						<span><?php echo round( $full_image_data['optimized_size'] / 1024, 2 ) . 'KB'; ?></span>
					</div>
					<div>
						<strong><?php _e( 'Save: ', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
						<span><?php echo $full_image_data['percent'] . '%'; ?></span>
					</div>
					<div>
						<button class="button button-sacondary quickwebp-undo-single-optimization-btn" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
							<?php _e( 'Undo optimization', QUICKWEBP_TEXT_DOMAIN ); ?>
							<div class="spinner"></div>
						</button>
						<div class="quickwebp-undo-single-optimization-msg"></div>
					</div>
				<?php

			return ob_get_clean();
		}

	}

	/**
	 * Display optimize button in the media uploader
	 */
	public function optimize_btn( $attachment_id ) {

		ob_start();

			?>
				<button class="button button-sacondary quickwebp-single-optimization-btn" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
					<?php _e( 'Optimize', QUICKWEBP_TEXT_DOMAIN ); ?>
					<div class="spinner"></div>
				</button>
				<div class="quickwebp-single-optimization-msg"></div>
			<?php

		return ob_get_clean();
	}

	/**
	 * Check the mimetype of the file
	 */
	public function valid_mimetype( $post_id ) {

		// check the mime type
		$image_optimizer	= new Quickwebp_Image_Optimizer( $this->plugin_name, $this->version );
		$mime_types			= $image_optimizer->allowed_mime_types;
		$index 				= array_search( 'image/webp', $mime_types );
		unset( $mime_types[$index] );
		$mime_type 			= get_post_mime_type( $post_id );

		return in_array( $mime_type, $mime_types );
	}

}