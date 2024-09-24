<?php
/**
 * The admin settings of the plugin.
 * @since      1.0.0
 */
?>
<h1><?php _e( "QuickWebp Settings", QUICKWEBP_TEXT_DOMAIN ); ?></h1>

<form action="" method="post">

    <table class="form-table">

        <?php $this->render_component( array(
            'type'      => 'toggle',
            'name'      => 'quickwebp_settings_conversion',
            'label'     => __( "Enable/disable image conversion to WEBP format", QUICKWEBP_TEXT_DOMAIN ),
            'default'   => quickwebp_settings_default('quickwebp_settings_conversion'),
            'classes'   => 'toggle-with-children',
        )); ?>

        <tbody class="form-table children">

            <?php $this->render_component( array(
                'type'        => 'range-slider',
                'min'         => 0,
                'max'         => 100,
                'step'        => 1,
                'unit'        => '%',
                'name'        => 'quickwebp_settings_conversion_quality',
                'label'       => __( 'Quality', QUICKWEBP_TEXT_DOMAIN ),
                'default'     => quickwebp_settings_default('quickwebp_settings_conversion_quality')
            )); ?>

            <?php $this->render_component( array(
                'type'        => 'range-slider',
                'min'         => 0,
                'max'         => 100,
                'step'        => 1,
                'unit'        => '%',
                'name'        => 'quickwebp_settings_conversion_sharpen',
                'label'       => __( 'Sharpen', QUICKWEBP_TEXT_DOMAIN ),
                'default'     => quickwebp_settings_default('quickwebp_settings_conversion_sharpen')
            )); ?>

            <?php $this->render_component( array(
                'type'      => 'checkbox',
                'name'      => 'quickwebp_settings_conversion_ignore_webp',
                'label'     => __( "Do not compress images already in WebP", QUICKWEBP_TEXT_DOMAIN ),
                'default'   => quickwebp_settings_default('quickwebp_settings_conversion_ignore_webp'),
                'options'   => array(
                    array(
                        'label' => '',
                        'value' => 'checked'
                    )
                )
            )); ?>

            <?php $this->render_component( array(
                'type'      => 'checkbox',
                'name'      => 'quickwebp_settings_conversion_save_original',
                'label'     => __( "Save original images", QUICKWEBP_TEXT_DOMAIN ),
                'default'   => quickwebp_settings_default('quickwebp_settings_conversion_save_original'),
                'options'   => array(
                    array(
                        'label' => '',
                        'value' => 'checked'
                    )
                )
            )); ?>

            <tr>
                <th>
                    <label><?php _e( 'Before saving, test your configuration with preview mode.', QUICKWEBP_TEXT_DOMAIN ); ?></label>
                </th>
                <td>
                    <?php include QUICKWEBP_PLUGIN_PATH . 'admin/templates/popup-settings-preview.php'; ?>
                </td>
            </tr>

            <?php
                $description_for_nginx = $is_nginx ? __("If you choose to use rewrite rules, the file conf/quickwebp.conf will be created and must be included into the server's configuration file (then restart the server).", QUICKWEBP_TEXT_DOMAIN) : '';

                $this->render_component( array(
                    'type'          => 'radio',
                    'name'          => 'quickwebp_settings_conversion_display_webp_mode',
                    'label'         => __( "Display images in WebP format on the site", QUICKWEBP_TEXT_DOMAIN ),
                    'description'   => sprintf( __('If activated, this option allows to deliver optimized images in bulk via QuickWebP in WebP format (useless for images converted to import). %s', QUICKWEBP_TEXT_DOMAIN), $description_for_nginx ),
                    'default'       => quickwebp_settings_default('quickwebp_settings_conversion_display_webp_mode'),
                    'options'       => array(
                        array(
                            'label' => __( 'Deactivate', QUICKWEBP_TEXT_DOMAIN ),
                            'value' => 'disabled'
                        ),
                        array(
                            'label' => __( 'Use <picture> tags', QUICKWEBP_TEXT_DOMAIN ),
                            'value' => 'picture'
                        ),
                        array(
                            'label' => sprintf( __( 'Use rewrite rules %s', QUICKWEBP_TEXT_DOMAIN ), $is_nginx ? '(beta)' : '' ),
                            'value' => 'rewrite'
                        )
                    )
                ));
            ?>

            <tr>
                <th>
                    <label><?php _e( 'Bulk optimization', QUICKWEBP_TEXT_DOMAIN ); ?></label>
                </th>
                <td>
                    <?php include QUICKWEBP_PLUGIN_PATH . 'admin/templates/bulk-optimization.php'; ?>
                </td>
            </tr>

        </tbody>

    </table>

    <hr>
    
    <table class="form-table">

        <?php $this->render_component( array(
            'type'      => 'toggle',
            'name'      => 'quickwebp_settings_resize',
            'label'     => __( "Enable/disable image resizing", QUICKWEBP_TEXT_DOMAIN ),
            'default'   => quickwebp_settings_default('quickwebp_settings_resize'),
            'classes'   => 'toggle-with-children',
            'description' => __( "By default, WordPress limits the maximum width of uploaded images to 2560 pixels.", QUICKWEBP_TEXT_DOMAIN ),
        )); ?>

        <tbody class="form-table children">

            <?php $this->render_component( array(
                'type'        => 'number',
                'name'        => 'quickwebp_settings_resize_value',
                'label'       => __( 'Max size', QUICKWEBP_TEXT_DOMAIN ),
                'default'     => quickwebp_settings_default('quickwebp_settings_resize_value')
            )); ?>

        </tbody>

    </table>

    <hr>

    <table class="form-table">

        <?php $this->render_component( array(
            'type'      => 'toggle',
            'name'      => 'quickwebp_settings_completion',
            'label'     => __( "Enable/disable smart media completion for SEO", QUICKWEBP_TEXT_DOMAIN ),
            'default'   => quickwebp_settings_default('quickwebp_settings_completion'),
            'classes'   => 'toggle-with-children',
            'description' => __( "This feature will automatically complete the media information (title, caption, alt text, description) from the image name.", QUICKWEBP_TEXT_DOMAIN ),
        )); ?>

        <tbody class="form-table children">

            <?php $this->render_component( array(
                'type'        => 'checkbox',
                'name'        => 'quickwebp_settings_completion_options',
                'default'     => quickwebp_settings_default('quickwebp_settings_completion_options'),
                'options'    => array(
                    array(
                        'label' => __( 'Title completion from image name', QUICKWEBP_TEXT_DOMAIN ),
                        'value' => 'title',
                    ),
                    array(
                        'label' => __( 'Caption completion from image name.', QUICKWEBP_TEXT_DOMAIN ),
                        'value' => 'caption',
                    ),
                    array(
                        'label' => __( 'Alt text completion from image name.', QUICKWEBP_TEXT_DOMAIN ),
                        'value' => 'alt',
                    ),
                    array(
                        'label' => __( 'Description completion from image name.', QUICKWEBP_TEXT_DOMAIN ),
                        'value' => 'description',
                    )
                )
            )); ?>

        </tbody>

    </table>

    <hr>

    <table class="form-table">

        <?php $this->render_component( array(
            'type'        => 'toggle',
            'name'        => 'quickwebp_settings_cleanup',
            'label'       => __( "Enable/disable file name cleanup", QUICKWEBP_TEXT_DOMAIN ),
            'default'     => quickwebp_settings_default('quickwebp_settings_cleanup'),
            'description' => __( "Remove special characters from file names.", QUICKWEBP_TEXT_DOMAIN ),
        )); ?>

    </table>
    
    <hr>

    <table class="form-table">

        <?php $this->render_component( array(
            'type'        => 'toggle',
            'name'        => 'quickwebp_settings_paste_image',
            'label'       => __( "Enable/disable paste picture directly (beta)", QUICKWEBP_TEXT_DOMAIN ),
            'default'     => quickwebp_settings_default('quickwebp_settings_paste_image'),
            'description' => __( "With this feature you can paste directly your picture in WordPress media.", QUICKWEBP_TEXT_DOMAIN ),
        )); ?>

    </table>

    <hr>

    <table class="form-table">

        <?php $this->render_component( array(
            'type'        => 'select',
            'options'     => array(
                array(
                    'value' => 'gd',
                    'label' => 'GD'
                ),
                array(
                    'value' => 'imagick',
                    'label' => 'Imagick'
                )
            ),
            'name'        => 'quickwebp_settings_library',
            'label'       => __( "Library to use", QUICKWEBP_TEXT_DOMAIN ),
            'default'     => quickwebp_settings_default('quickwebp_settings_library'),
            'description' => __( "We use the GD library as the default option. However, if the GD library is not available, we will use Imagick instead.", QUICKWEBP_TEXT_DOMAIN )
        )); ?>

    </table>

    <hr>
    
    <h2>
        <?php _e( "Credits", QUICKWEBP_TEXT_DOMAIN ); ?>
    </h2>
    <p>
        <?php _e( "This plugin is developed by", QUICKWEBP_TEXT_DOMAIN ); ?>
        <a href="https://webdeclic.com/" target="_blank">Webdeclic</a>.
        <?php _e( "You can support this project here:", QUICKWEBP_TEXT_DOMAIN ); ?>
    </p>
    <p>
        <a class="buymeacoffee" href="https://www.buymeacoffee.com/ludwig" target="_blank"><img style="height: 60px;" src="<?php echo esc_url( QUICKWEBP_PLUGIN_URL . 'public/assets/img/buy-me-a-coffee.webp' ); ?>" alt="Buy Me A Coffee"></a>
    </p>
    <p>
        <?php _e( "You can show all Webdeclic's plugins on ", QUICKWEBP_TEXT_DOMAIN ); ?>
        <a href="https://wordpress.org/plugins/search/webdeclic/" target="_blank"><?php _e( "wordpress.org", QUICKWEBP_TEXT_DOMAIN ); ?></a>.
    </p>

    <hr>

    <?php submit_button(); ?>
</form>