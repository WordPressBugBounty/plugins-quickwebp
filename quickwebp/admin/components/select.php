<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Select component for the settings page
 */

$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_value_to_save = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
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
    <select 
        name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
        id="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>"
    >
        <?php if( isset( $data['placeholder'] ) ): ?>
            <option value="">
                <?php echo esc_html( $data['placeholder'] ?? __( 'Select an option', 'quickwebp' ) ); ?>
            </option>
        <?php endif; ?>

        <?php
        $quickwebp_option_saved = get_option( $quickwebp_key_option, $data['default'] );
        foreach( $data['options'] as $quickwebp_option ) {
            ?>
            <option 
                value="<?php echo esc_attr( $quickwebp_option['value'] ?? '' ); ?>"
                <?php selected( $quickwebp_option['value'], $quickwebp_option_saved ); ?>
            >
                <?php echo esc_html( $quickwebp_option['label'] ?? '' ); ?>
            </option>
            <?php
        }
        ?>
    </select>
    <?php
}