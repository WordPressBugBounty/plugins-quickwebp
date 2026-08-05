<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The preview poupup in the settings page
 * @since      1.0.0
 */
?>
<div class="quickwebp-preview">
    <div class="quickwebp-preview__demo">
    	<div class="quickwebp-preview__demo__top">
    		<div class="quickwebp-preview__demo__top__left">
    			<div class="quickwebp-preview__demo__top__left__image">
					<img src="<?php echo esc_url( QUICKWEBP_PLUGIN_URL . 'public/assets/img/preview.jpg' ); ?>" alt="Preview" />
					<div class="quickwebp-preview__demo__top__left__image__edit">
						<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/edit-square-outline.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
					</div>
				</div>
			</div>
    		<div class="quickwebp-preview__demo__top__right">
    			<div class="quickwebp-preview__demo__top__right__title"><?php esc_html_e( 'Preview on your typical photo', 'quickwebp' ); ?></div>
				<div class="quickwebp-preview__demo__top__right__info"><?php echo esc_html( $preview_image_data['name'] ); ?> · <?php echo esc_html( $preview_image_data['size'] ); ?> · <?php echo esc_html( $preview_image_data['dimensions'] ); ?></div>
				<div class="quickwebp-preview__demo__top__right__details webp <?php echo ( '1' == $conversion ) ? 'selected' : ''; ?>">
					<?php echo wp_kses_post( sprintf(
						// translators: %s is the amount of space saved by converting to WebP format.
						__( 'WebP saves %s on this photo', 'quickwebp' ), '<span class="value">' . esc_html( $preview_image_data_webp['save'] ) . '</span>'
					) ); ?>
				</div>
				<div class="quickwebp-preview__demo__top__right__details avif <?php echo ( '2' == $conversion ) ? 'selected' : ''; ?>">
					<?php echo wp_kses_post( sprintf(
						// translators: %s is the amount of space saved by converting to AVIF format.
						__( 'AVIF saves %s on this photo', 'quickwebp' ), '<span class="value">' . esc_html( $preview_image_data_avif['save'] ) . '</span>'
					) ); ?>
				</div>
			</div>
		</div>
		<div class="quickwebp-preview__demo__bottom">
			<div class="quickwebp-preview__demo__bottom__item">
				<div class="quickwebp-preview__demo__bottom__item__title"><?php esc_html_e( 'Original', 'quickwebp' ); ?></div>
				<div class="quickwebp-preview__demo__bottom__item__progress">
					<div class="quickwebp-preview__demo__bottom__item__progress__bar original"><?php echo esc_html( $preview_image_data['size'] ); ?></div>
				</div>
				<div class="quickwebp-preview__demo__bottom__item__info">
					<div class="quickwebp-preview__demo__bottom__item__info__save"><?php esc_html_e( '0% saved', 'quickwebp' ); ?></div>
					<div class="quickwebp-preview__demo__bottom__item__info__desc"><?php esc_html_e( 'No compression applied', 'quickwebp' ); ?></div>
				</div>
			</div>

			<div class="quickwebp-preview__demo__bottom__item webp <?php echo ( '1' == $conversion ) ? 'selected' : ''; ?>">
				<div class="quickwebp-preview__demo__bottom__item__title"><?php esc_html_e( 'WebP', 'quickwebp' ); ?></div>
				<div class="quickwebp-preview__demo__bottom__item__progress">
					<div class="quickwebp-preview__demo__bottom__item__progress__bar webp" style="width: <?php echo esc_attr( $preview_image_data_webp['percent'] ); ?>;"><?php echo esc_html( $preview_image_data_webp['size'] ); ?></div>
				</div>
				<div class="quickwebp-preview__demo__bottom__item__info">
					<div class="quickwebp-preview__demo__bottom__item__info__save webp tag">
						<span class="value"><?php echo esc_html( $preview_image_data_webp['save_percent'] ); ?></span>
						<span class="small"><?php esc_html_e( 'on this photo', 'quickwebp' ); ?></span>
					</div>
					<div class="quickwebp-preview__demo__bottom__item__info__desc"><?php esc_html_e( '≈ −30% on average across a site', 'quickwebp' ); ?></div>
				</div>
				<div class="quickwebp-preview__demo__bottom__item__check">
					<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
				</div>
			</div>

			<div class="quickwebp-preview__demo__bottom__item avif <?php echo ( '2' == $conversion ) ? 'selected' : ''; ?> <?php echo ( ! $license_valid ) ? 'quickwebp__pro-popup-open' : ''; ?>">
				<div class="quickwebp-preview__demo__bottom__item__title"><?php esc_html_e( 'AVIF', 'quickwebp' ); ?></div>
				<div class="quickwebp-preview__demo__bottom__item__progress">
					<div class="quickwebp-preview__demo__bottom__item__progress__bar avif" style="width: <?php echo esc_attr( $preview_image_data_avif['percent'] ); ?>;"><?php echo esc_html( $preview_image_data_avif['size'] ); ?></div>
				</div>
				<div class="quickwebp-preview__demo__bottom__item__info">
					<div class="quickwebp-preview__demo__bottom__item__info__save avif tag">
						<span class="value"><?php echo esc_html( $preview_image_data_avif['save_percent'] ); ?></span>
						<span class="small"><?php esc_html_e( 'on this photo', 'quickwebp' ); ?></span>
					</div>
					<div class="quickwebp-preview__demo__bottom__item__info__desc"><?php esc_html_e( '≈ −50% on average across a site', 'quickwebp' ); ?></div>
				</div>
				<?php if ( ! $license_valid ): ?>
					<div class="quickwebp-preview__demo__bottom__item__pro"><?php esc_html_e( 'Pro', 'quickwebp' ); ?></div>
				<?php endif; ?>
				<div class="quickwebp-preview__demo__bottom__item__check">
					<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
				</div>
			</div>
		</div>
	</div>

    <div class="quickwebp-preview__popup">

        <div class="quickwebp-preview__popup__file">

            <div class="quickwebp-preview__popup__file__btn">
                <?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/add-img.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
                <span><?php esc_html_e( 'Add image', 'quickwebp' ); ?></span>
            </div>

            <input type="file" class="quickwebp-preview__popup__file__input" accept='image/*'>
        </div>

        <div class="quickwebp-preview__popup__compare show">

            <div class="quickwebp-preview__popup__compare__images">
                <div class="quickwebp-preview__popup__compare__images__original">
                    <div class="image"></div>
                </div>
                <div class="quickwebp-preview__popup__compare__images__new">
                    <div class="image"></div>
                </div>
            </div>

            <div class="quickwebp-preview__popup__compare__handle">
                <div class="quickwebp-preview__popup__compare__handle__svg">
                    <?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/resize.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
                </div>
            </div>

            <div class="quickwebp-preview__popup__compare__data">

                <div class="quickwebp-preview__popup__compare__data__original">
                    <div class="quickwebp-preview__popup__compare__data__original__type"><?php esc_html_e( 'Original Image', 'quickwebp' ); ?></div>
                    <div class="quickwebp-preview__popup__compare__data__original__size"></div>
                </div>

                <div class="quickwebp-preview__popup__compare__data__new">
                    <div class="quickwebp-preview__popup__compare__data__new__type"></div>
                    <div class="quickwebp-preview__popup__compare__data__new__size"></div>
                    <div class="quickwebp-preview__popup__compare__data__new__gain"></div>
                </div>

            </div>

			<div class="quickwebp-preview__popup__compare__zoom">
				<div class="quickwebp-preview__popup__compare__zoom__btn minus">
					<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/minus.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
				</div>
				<div class="quickwebp-preview__popup__compare__zoom__value">
					<div class="quickwebp-preview__popup__compare__zoom__value__text">100</div>
					<div class="quickwebp-preview__popup__compare__zoom__value__unit">%</div>
				</div>
				<div class="quickwebp-preview__popup__compare__zoom__btn plus">
					<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/plus.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
				</div>
			</div>
        </div>

        <div class="quickwebp-preview__popup__spiner">
            <div class="quickwebp-preview__popup__spiner__circle"></div>
        </div>

        <div class="quickwebp-preview__popup__close">
            <button class="quickwebp-preview__popup__close__btn"><?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/close.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?></button>
        </div>

        <div class="quickwebp-preview__popup__replace show">
			<div class="quickwebp-preview__popup__replace__btn">
                <?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/add-img.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
				<span><?php esc_html_e( 'Replace image', 'quickwebp' ); ?></span>
            </div>
        </div>
    </div>
</div>
