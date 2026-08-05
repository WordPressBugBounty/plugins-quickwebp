<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Toggle component like IOS style for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_raw_value     = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    $quickwebp_value_to_save = '1' === $quickwebp_raw_value ? '1' : '0';
    update_option( $quickwebp_key_option, $quickwebp_value_to_save );
}
?>
<label class="switch">
    <input type="hidden" name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>" value="0">
    <input 
        class="<?php echo esc_attr( $data['classes'] ?? ''); ?>"
        type="checkbox" 
        name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
        id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
        value="1"
        <?php checked( '1', get_option( $quickwebp_key_option, $data['default'] ?? '' ) ); ?>
    >
    <span class="slider round"></span>
</label>