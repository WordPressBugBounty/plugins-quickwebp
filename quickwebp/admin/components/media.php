<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Media component for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );
//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_raw_value = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    $quickwebp_media_ids = array_filter( array_map( 'absint', explode( ',', $quickwebp_raw_value ) ) );
    update_option( $quickwebp_key_option, implode( ',', $quickwebp_media_ids ) );
}

wp_enqueue_media();
$quickwebp_is_multiple = isset( $data['multiple'] ) && $data['multiple'] === true ? 'true' : 'false';
$quickwebp_image_preview_template = '<img src="%1$s" title="%2$s" class="media-preview-image">';

$quickwebp_medias_string = isset( $quickwebp_key_option ) ? get_option( $quickwebp_key_option, '' ) : '';
$quickwebp_medias        = $quickwebp_medias_string ? explode( ',', $quickwebp_medias_string ) : array();
?>
<div class="quickwebp-media-input-container">
    <div id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_is_empty" class="quickwebp-media-input--is-empty" style="display: <?php echo esc_attr( ! empty( $quickwebp_medias ) ? 'none' : 'block' ); ?>">
        <?php esc_html_e( 'No image selected', 'quickwebp' ); ?>
    </div>
    <input 
        type="hidden" 
        name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
        id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
        value="<?php echo esc_attr( $quickwebp_medias_string ); ?>"
    >
    <div id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_medias-selected" class="quickwebp-medias-selected-container">
        <?php
        foreach( $quickwebp_medias as $quickwebp_media_id ) {
            $quickwebp_media_id = absint( $quickwebp_media_id );
            $quickwebp_media = get_post( $quickwebp_media_id );
            if( $quickwebp_media ) {
                if( wp_attachment_is_image( $quickwebp_media_id ) ) {
                    echo wp_kses_post( sprintf( $quickwebp_image_preview_template, esc_url( wp_get_attachment_image_url( $quickwebp_media_id, 'thumbnail' ) ), esc_attr( $quickwebp_media->post_title ) ) );
                } else {
                    echo wp_kses_post( sprintf( $quickwebp_image_preview_template, esc_url( wp_mime_type_icon( $quickwebp_media->post_mime_type ) ), esc_attr( $quickwebp_media->post_title ) ) );
                }
            }
        }
        ?>
    </div>
    <input type="button" 
        name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_button"
        id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_button"
        class="button button-secondary"
        value="<?php echo esc_attr( $data['button_text'] ?? __( 'Select media', 'quickwebp' ) ); ?>"
    >
    <input type="button" 
        name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_remove_button"
        id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>_remove_button"
        class="button button-secondary button-remove-media"
        value="<?php echo esc_attr( $data['remove_button_text'] ?? __( 'Remove medias', 'quickwebp' ) ); ?>"
        <?php echo empty( $quickwebp_medias ) ? 'disabled' : ''; ?>
    >
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        const id = <?php echo wp_json_encode( $quickwebp_key_option ?? '' ); ?>;
        const media_template_preview = <?php echo wp_json_encode( $quickwebp_image_preview_template ); ?>;
        jQuery( "#" + id + "_button" ).click(function(e) {
            e.preventDefault();
            var image = wp.media({ 
                title: <?php echo wp_json_encode( $data['button_text'] ?? __( 'Select media', 'quickwebp' ) ); ?>,
                multiple: <?php echo esc_attr( $quickwebp_is_multiple ); ?>
            })

            image.on('select', function(e){
                const medias = image.state().get('selection').toJSON();
                let selected_medias_preview = '';
                let medias_ids = '';
                medias.forEach( media => {
                    medias_ids = medias_ids === '' ? media.id : medias_ids + ',' + media.id;
                    let preview_image = '';
                    if( media.type === 'image' ) {
                        preview_image = media.sizes.thumbnail.url;
                    } else {
                        preview_image = media.icon;
                    }
                    selected_medias_preview += media_template_preview.replace( '%s', preview_image ).replace( '%s', media.title );
                });
                jQuery( "#" + id + "_medias-selected" ).html( selected_medias_preview );
                jQuery( "#" + id ).val( medias_ids );
                
                jQuery( "#" + id + "_remove_button" ).prop( 'disabled', false );
                jQuery( '#' + id + "_is_empty" ).hide();
            });

            image.on('open', function(e){
                const medias_ids = jQuery( "#" + id ).val();
                if( medias_ids !== '' ) {
                    const medias = medias_ids.split(',');
                    const selection = image.state().get('selection');
                    medias.forEach( media_id => {
                        const media = wp.media.attachment( media_id );
                        media.fetch();
                        selection.add( media ? [ media ] : [] );
                    });
                }
            });

            image.open();
            
        });

        jQuery( "#" + id + "_remove_button" ).click(function(e) {
            e.preventDefault();
            jQuery( "#" + id + "_medias-selected" ).html( '' );
            jQuery( "#" + id ).val( '' );
            
            jQuery( "#" + id + "_remove_button" ).attr( 'disabled', 'disabled' );
            jQuery( '#' + id + "_is_empty" ).show();
        });
    });
</script>