<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Checkbox component for the settings page
 */
$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {

	//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $quickwebp_raw_value = wp_unslash( $_POST[ $quickwebp_key_option ] );
    $quickwebp_value     = is_array( $quickwebp_raw_value ) ? array_map( 'sanitize_text_field', $quickwebp_raw_value ) : array();
    update_option( $quickwebp_key_option, $quickwebp_value );
    
}
if(isset($data['options']) && is_array($data['options'])) {
    ?>
    <input type="hidden" name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>" value="">
    <div class="quickwebp-options">
        <?php
        $quickwebp_option_saved = get_option( $quickwebp_key_option, $data['default'] );
        $quickwebp_option_saved = is_array( $quickwebp_option_saved ) ? $quickwebp_option_saved : array();
        foreach( $data['options'] as $quickwebp_key => $quickwebp_option ) {
            ?>
            <label>
                <input
                    type="checkbox" 
                    name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>[]"
                    id="<?php echo esc_attr( ($quickwebp_key_option ?? '') . "-$quickwebp_key" ); ?>"
                    value="<?php echo esc_attr( $quickwebp_option['value'] ?? '' ); ?>"
                    <?php checked( in_array( $quickwebp_option['value'], $quickwebp_option_saved ) ); ?>
                >
                <?php echo esc_html( $quickwebp_option['label'] ?? '' ); ?>
            </label>
            <?php
        }
        ?>
    </div>
    <?php
}