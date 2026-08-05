<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Button group component for the settings page
 */
$quickwebp_key_option = sanitize_key( $data['name'] );

//phpcs:ignore WordPress.Security.NonceVerification.Missing
if ( isset( $quickwebp_key_option, $_POST[ $quickwebp_key_option ] ) ) {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing
    $quickwebp_value_to_save = sanitize_text_field( wp_unslash( $_POST[ $quickwebp_key_option ] ) );
    update_option( $quickwebp_key_option, $quickwebp_value_to_save );
}

$quickwebp_conversion = get_option( $quickwebp_key_option, quickwebp_settings_default( $quickwebp_key_option ) );

if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
	?><span class="quickwebp-button-group"><?php

	foreach( $data['options'] as $quickwebp_option ) {
		?>
		<label class="quickwebp-button-group__item <?php echo esc_attr( $quickwebp_option['parent_classes'] ?? '' ); ?>">
			<input class="<?php echo esc_attr( $quickwebp_option['classes'] ?? ''); ?>" type="radio" name="<?php echo esc_attr( $quickwebp_key_option ?? '' ); ?>" value="<?php echo esc_attr( $quickwebp_option['value'] ?? '' ); ?>" <?php checked( $quickwebp_conversion, $quickwebp_option['value'] ?? '' ); disabled( isset( $quickwebp_option['disabled'] ) && $quickwebp_option['disabled'] ); ?>>
			<span class="quickwebp-button-group__item__content">
				<span class="quickwebp-button-group__item__content__text" style="color: <?php echo esc_attr( $quickwebp_option['color'] ?? '' ); ?>;">
					<?php echo esc_html( $quickwebp_option['label'] ?? '' ); ?>
				</span>
				<span class="quickwebp-button-group__item__content__desc">
					<?php echo esc_html( $quickwebp_option['desc'] ?? '' ); ?>
				</span>
			</span>
			<span class="quickwebp-button-group__item__check">
				<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
			</span>
			<?php
			if ( isset( $quickwebp_option['taglines'] ) && is_array( $quickwebp_option['taglines']) ) {
				foreach( $quickwebp_option['taglines'] as $quickwebp_tagline ) {
					if ( isset( $quickwebp_tagline['show'] ) && ! $quickwebp_tagline['show'] ) {
						continue;
					}
					?>
					<span class="quickwebp-button-group__item__tagline <?php echo esc_attr( strtolower( $quickwebp_tagline['value'] ?? '' ) ); ?>">
						<?php echo esc_html( $quickwebp_tagline['label'] ?? '' ); ?>
					</span>
					<?php
				}
			}
			?>
		</label>
	<?php
	}

	?></span><?php
}
