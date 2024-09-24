<?php
/**
 * The preview poupup in the settings page
 * @since      1.0.0
 */
?>
<div class="quickwebp-preview">

    <div class="quickwebp-preview__open">
        <button class="quickwebp-preview__open__btn button button-secondary"><?php _e( 'Preview', QUICKWEBP_TEXT_DOMAIN ); ?></button>
    </div>

    <div class="quickwebp-preview__popup">

        <div class="quickwebp-preview__popup__file show">

            <div class="quickwebp-preview__popup__file__btn">
                <?php echo file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/add-img.svg' ); ?>
                <span><?php _e( 'Add image', QUICKWEBP_TEXT_DOMAIN ); ?></span>
            </div>

            <input type="file" class="quickwebp-preview__popup__file__input" accept='image/*'>
        </div>

        <div class="quickwebp-preview__popup__compare">

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
                    <?php echo file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/resize.svg' ); ?>
                </div>
            </div>

            <div class="quickwebp-preview__popup__compare__data">

                <div class="quickwebp-preview__popup__compare__data__original">
                    <div class="quickwebp-preview__popup__compare__data__original__type"><?php _e( 'Original Image', QUICKWEBP_TEXT_DOMAIN ); ?></div>
                    <div class="quickwebp-preview__popup__compare__data__original__size"></div>
                </div>

                <div class="quickwebp-preview__popup__compare__data__new">
                    <div class="quickwebp-preview__popup__compare__data__new__type"></div>
                    <div class="quickwebp-preview__popup__compare__data__new__size"></div>
                    <div class="quickwebp-preview__popup__compare__data__new__gain"></div>
                </div>

            </div>

        </div>

        <div class="quickwebp-preview__popup__spiner">
            <div class="quickwebp-preview__popup__spiner__circle"></div>
        </div>

        <div class="quickwebp-preview__popup__close">
            <button class="quickwebp-preview__popup__close__btn"><?php echo file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/close.svg' ); ?></button>
        </div>

    </div>

</div>