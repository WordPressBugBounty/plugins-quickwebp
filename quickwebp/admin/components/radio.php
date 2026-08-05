<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Radio component for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_value_to_save  = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    $quickwebp_allowed_values = array();

    if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
        foreach ( $data['options'] as $quickwebp_option ) {
            $quickwebp_allowed_values[] = isset( $quickwebp_option['value'] ) ? (string) $quickwebp_option['value'] : '';
        }
    }

    if ( in_array( $quickwebp_value_to_save, $quickwebp_allowed_values, true ) ) {
        update_option( $quickwebp_key_option, $quickwebp_value_to_save );
    }
}
if(isset($data['options']) && is_array($data['options'])) {
    ?>
    <input type="hidden" name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>" value="">
    <div class="quickwebp-options">
        <?php
        foreach( $data['options'] as $quickwebp_option ) {
            ?>
            <label>
                <input 
                    type="radio" 
                    name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
                    id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
                    value="<?php echo esc_attr( $quickwebp_option['value'] ?? '' ); ?>"
                    <?php checked( $quickwebp_option['value'], get_option( $quickwebp_key_option, $data['default'] ) ); ?>
                >
                <?php echo esc_html( $quickwebp_option['label'] ?? '' ); ?>
            </label>
            <?php
        }
        ?>
    </div>
    <?php
}