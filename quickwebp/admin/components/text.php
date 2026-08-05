<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Text component for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_value_to_save = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    update_option( $quickwebp_key_option, $quickwebp_value_to_save );
}
?>
<input 
    type="<?php echo esc_attr( $data['type'] ); ?>"
    name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
    class="regular-text"
    id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
    value="<?php echo esc_html( get_option( $quickwebp_key_option ?? '', $data['default'] ?? '' ) ); ?>"
    <?php echo isset( $data['pattern'] ) ? 'pattern="' . esc_attr( $data['pattern'] ) . '"' : ''; ?>
>