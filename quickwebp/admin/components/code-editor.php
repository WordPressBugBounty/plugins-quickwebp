<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Code editor component like theme editor for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_value_to_save = wp_kses_post( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    update_option( $quickwebp_key_option, $quickwebp_value_to_save );
}
?>
<textarea 
    name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
    id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
><?php echo esc_textarea( stripslashes(  get_option( $quickwebp_key_option ?? '' ) ) ); ?></textarea>

<style>
    <?php echo '.' . esc_attr( $quickwebp_key_option ) . '-container'; ?> > .CodeMirror {
        height: <?php echo esc_attr( $data['height'] ?? "auto" ); ?>;
    }
</style>

<?php
$quickwebp_settings = wp_enqueue_code_editor( array( 'type' => $data['mime_type'] ?? 'text/html' ) );

if ( $quickwebp_settings ) {
    wp_add_inline_script(
        'code-editor',
        sprintf(
            'jQuery( function() { wp.codeEditor.initialize( %1$s, %2$s ); } );',
            wp_json_encode( (string) $quickwebp_key_option ),
            wp_json_encode( $quickwebp_settings )
        )
    );
}
