<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin settings of the plugin.
 * @since      1.0.0
 */
?>
<div class="quickwebp-settings-v2" data-current-profile="<?php echo esc_attr( $profile_label ); ?>">

    <header class="quickwebp-hero">
        <h1><?php esc_html_e( 'QuickWebP Settings', 'quickwebp' ); ?></h1>
        <p><?php esc_html_e( 'Configure your image workflow with coherent presets, guarded options, and an optimized media delivery strategy.', 'quickwebp' ); ?></p>
    </header>

    <form action="" method="post" class="quickwebp-settings-form">
        <div class="quickwebp-layout">
            <main class="quickwebp-main">
                <?php if ( $show_onboarding ) : ?>
                    <section class="quickwebp-card" id="quickwebp-onboarding">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Quick setup assistant', 'quickwebp' ); ?></h2>
                            <p><?php esc_html_e( 'Start from a recommended profile and refine only what you need.', 'quickwebp' ); ?></p>
                        </div>
                        <div class="quickwebp-card-body">
                            <div class="quickwebp-onboarding-grid">
                                <button type="button" class="quickwebp-onboarding-option quickwebp-onboarding-apply" data-profile="new-site">
                                    <strong><?php esc_html_e( 'New site with few media', 'quickwebp' ); ?></strong>
                                    <span><?php esc_html_e( 'Replace originals on upload for the simplest and fastest workflow.', 'quickwebp' ); ?></span>
                                </button>
                                <button type="button" class="quickwebp-onboarding-option quickwebp-onboarding-apply" data-profile="existing-site">
                                    <strong><?php esc_html_e( 'Existing site with many media', 'quickwebp' ); ?></strong>
                                    <span><?php esc_html_e( 'Keep originals, enable rewrite delivery, and run bulk optimization.', 'quickwebp' ); ?></span>
                                </button>
                            </div>
                            <p class="quickwebp-onboarding-feedback" aria-live="polite"></p>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="quickwebp-card" id="quickwebp-conversion">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Conversion and Delivery', 'quickwebp' ); ?></h2>
                        <p><?php esc_html_e( 'Define how images are converted, preserved, and delivered on frontend pages.', 'quickwebp' ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
							
                            <?php $this->render_component( array(
                                'type'      => 'button-group',
                                'name'      => 'quickwebp_settings_conversion',
                                'label'     => __( 'Image Format Optimization', 'quickwebp' ),
                                'default'   => quickwebp_settings_default( 'quickwebp_settings_conversion' ),
								'options'   => array(
									array(
										'label'   => __( 'Off', 'quickwebp' ),
										'color'   => '#000000',
										'desc'    => __( '0% saved', 'quickwebp' ),
										'value'   => '0',
										'classes' => 'toggle-with-children',
									),
									array(
										'label'   => __( 'WebP', 'quickwebp' ),
										'color'   => '#316BFF',
										'desc'    => __( '≈ −30% on average across a site', 'quickwebp' ),
										'value'   => '1',
										'classes' => 'toggle-with-children',
									),
									array(
										'label'          => __( 'AVIF', 'quickwebp' ),
										'color'          => '#16A34A',
										'desc'           => __( '≈ −50% on average across a site', 'quickwebp' ),
										'value'          => '2',
										'classes'        => 'toggle-with-children',
										'parent_classes' => 'quickwebp__pro-popup-open',
										'disabled'       => ! $license_valid || ! $php_version_valid,
										'taglines'       => array(
											array(
												'label' => 'Pro',
												'value' => 'pro',
												'show'  => ! $license_valid,
											),
											array(
												'label' => 'Requires PHP 8.1+',
												'value' => 'pro',
												'show'  => ! $php_version_valid,
											),
											array(
												'label' => __( 'Recommended', 'quickwebp' ),
												'value' => 'recommended',
												'show'  => true,
											),
										),
									)
								)
                            ) ); ?>

                            <tbody class="form-table children">

								<?php $this->render_component( array(
									'type'      => 'button-group',
									'name'      => 'quickwebp_settings_conversion_quality',
									'label'     => __( 'Image Quality', 'quickwebp' ),
									'default'   => quickwebp_settings_default( 'quickwebp_settings_conversion_quality' ),
									'options'   => array(
										array(
											'label' => __( 'Low', 'quickwebp' ),
											'value' => 'low',
											'color' => '#E74C3C',
											'desc'  => __( 'Smallest size', 'quickwebp' ),
										),
										array(
											'label' => __( 'Medium', 'quickwebp' ),
											'value' => 'medium',
											'color' => '#E67E22',
											'desc'  => __( 'Good balance', 'quickwebp' ),
										),
										array(
											'label'    => __( 'High', 'quickwebp' ),
											'value'    => 'high',
											'color'    => '#16A34A',
											'desc'     => __( 'High quality & good size', 'quickwebp' ),
											'taglines' => array(
												array(
													'label' => __( 'Recommended', 'quickwebp' ),
													'value' => 'recommended',
													'show'  => true,
												),
											),
										),
										array(
											'label' => __( 'Extra High', 'quickwebp' ),
											'value' => 'extra_high',
											'color' => '#2563EB',
											'desc'  => __( 'Maximum quality', 'quickwebp' ),
										),
									)
								) ); ?>

								<tr>
									<th><label><?php esc_html_e( 'Preview', 'quickwebp' ); ?></label></th>
                                    <td>
                                        <?php include QUICKWEBP_PLUGIN_PATH . 'admin/templates/popup-settings-preview.php'; ?>
                                    </td>
                                </tr>

                                <?php $this->render_component( array(
                                    'type'      => 'checkbox',
                                    'name'      => 'quickwebp_settings_conversion_ignore_webp',
                                    'label'     => __( 'Do not compress images already in same format', 'quickwebp' ),
                                    'default'   => quickwebp_settings_default( 'quickwebp_settings_conversion_ignore_webp' ),
                                    'options'   => array(
                                        array(
                                            'label' => '',
                                            'value' => 'checked'
                                        )
                                    )
                                ) ); ?>

                                <?php $this->render_component( array(
                                    'type'      => 'checkbox',
                                    'name'      => 'quickwebp_settings_conversion_save_original',
                                    'label'     => __( 'Save original images', 'quickwebp' ),
                                    'default'   => quickwebp_settings_default( 'quickwebp_settings_conversion_save_original' ),
                                    'options'   => array(
                                        array(
                                            'label' => '',
                                            'value' => 'checked'
                                        )
                                    )
                                ) ); ?>

                                <tr class="quickwebp-guidance-row">
                                    <th>
                                        <label><?php esc_html_e( 'Consistency guidance', 'quickwebp' ); ?></label>
                                    </th>
                                    <td>
                                        <div class="quickwebp-guidance-box">
                                            <p class="quickwebp-guidance-message" aria-live="polite"></p>
                                        </div>
                                    </td>
                                </tr>

                                <?php
                                    $quickwebp_description_for_nginx = $is_nginx ? __( "If you choose to use rewrite rules, the file conf/quickwebp.conf will be created and must be included into the server's configuration file (then restart the server).", 'quickwebp' ) : '';

                                    $this->render_component( array(
                                        'type'          => 'radio',
                                        'name'          => 'quickwebp_settings_conversion_display_webp_mode',
                                        'label'         => __( 'Display images in WebP format on the site', 'quickwebp' ),
                                        'description'   => sprintf(
											// translators: %s is a placeholder for the description for nginx.
											__( 'If activated, this option allows to deliver optimized images in bulk via QuickWebP in WebP format (useless for images converted to import). %s', 'quickwebp' ),
											$quickwebp_description_for_nginx
										),
                                        'default'       => quickwebp_settings_default( 'quickwebp_settings_conversion_display_webp_mode' ),
                                        'options'       => array(
                                            array(
                                                'label' => __( 'Deactivate', 'quickwebp' ),
                                                'value' => 'disabled'
                                            ),
                                            array(
                                                'label' => __( 'Use <picture> tags', 'quickwebp' ),
                                                'value' => 'picture'
                                            ),
                                            array(
                                                'label' => sprintf( 
													// translators: %s is a placeholder for the text "(beta)" if the server is Nginx.
													__( 'Use rewrite rules %s', 'quickwebp' ), 
													$is_nginx ? '(beta)' : ''
												),
                                                'value' => 'rewrite'
                                            )
                                        )
                                    ) );
                                ?>

                                <tr class="quickwebp-display-warning-row" style="display:none;">
                                    <th>
                                        <label><?php esc_html_e( 'Why this is locked', 'quickwebp' ); ?></label>
                                    </th>
                                    <td>
                                        <p class="quickwebp-display-warning"></p>
                                    </td>
                                </tr>

                                <tr class="quickwebp-row-bulk-optimization">
                                    <th>
                                        <label><?php esc_html_e( 'Bulk optimization', 'quickwebp' ); ?></label>
                                    </th>
                                    <td>
                                        <?php include QUICKWEBP_PLUGIN_PATH . 'admin/templates/bulk-optimization.php'; ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-resize">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Resize', 'quickwebp' ); ?></h2>
                        <p><?php esc_html_e( 'Keep your uploads under control to reduce bandwidth and processing costs.', 'quickwebp' ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_resize',
                                'label'       => __( 'Enable/disable image resizing', 'quickwebp' ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_resize' ),
                                'classes'     => 'toggle-with-children',
                                'description' => __( 'By default, WordPress limits the maximum width of uploaded images to 2560 pixels.', 'quickwebp' ),
                            ) ); ?>

                            <tbody class="form-table children">
                                <?php $this->render_component( array(
                                    'type'        => 'number',
                                    'name'        => 'quickwebp_settings_resize_value',
                                    'label'       => __( 'Max size', 'quickwebp' ),
                                    'default'     => quickwebp_settings_default( 'quickwebp_settings_resize_value' )
                                ) ); ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-seo">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'SEO metadata automation', 'quickwebp' ); ?></h2>
                        <p><?php esc_html_e( 'Auto-fill media fields from image names for a faster editorial workflow.', 'quickwebp' ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_completion',
                                'label'       => __( 'Enable/disable smart media completion for SEO', 'quickwebp' ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_completion' ),
                                'classes'     => 'toggle-with-children',
                                'description' => __( 'This feature will automatically complete the media information (title, caption, alt text, description) from the image name.', 'quickwebp' ),
                            ) ); ?>

                            <tbody class="form-table children">
                                <?php $this->render_component( array(
                                    'type'      => 'checkbox',
                                    'name'      => 'quickwebp_settings_completion_options',
                                    'default'   => quickwebp_settings_default( 'quickwebp_settings_completion_options' ),
                                    'options'   => array(
                                        array(
                                            'label' => __( 'Title completion from image name', 'quickwebp' ),
                                            'value' => 'title',
                                        ),
                                        array(
                                            'label' => __( 'Caption completion from image name.', 'quickwebp' ),
                                            'value' => 'caption',
                                        ),
                                        array(
                                            'label' => __( 'Alt text completion from image name.', 'quickwebp' ),
                                            'value' => 'alt',
                                        ),
                                        array(
                                            'label' => __( 'Description completion from image name.', 'quickwebp' ),
                                            'value' => 'description',
                                        )
                                    )
                                ) ); ?>
                            </tbody>
                        </table>

                        <div class="quickwebp-seo-example">
                            <h3><?php esc_html_e( 'Live example', 'quickwebp' ); ?></h3>
                            <p><?php esc_html_e( 'Use a descriptive filename once (including spaces or apostrophes), and metadata is auto-filled from that original filename without the extension.', 'quickwebp' ); ?></p>
                            <input
                                type="text"
                                class="quickwebp-seo-example-input"
                                value="Men's Summer Shoes - Limited Edition 2026.jpg"
                                aria-label="<?php echo esc_attr__( 'SEO metadata example filename', 'quickwebp' ); ?>"
                            >

                            <div class="quickwebp-seo-example-grid">
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Title', 'quickwebp' ); ?></strong>
                                    <span class="quickwebp-seo-preview-title"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Caption', 'quickwebp' ); ?></strong>
                                    <span class="quickwebp-seo-preview-caption"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Alt text', 'quickwebp' ); ?></strong>
                                    <span class="quickwebp-seo-preview-alt"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Description', 'quickwebp' ); ?></strong>
                                    <span class="quickwebp-seo-preview-description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-tools">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Other tools', 'quickwebp' ); ?></h2>
                        <p><?php esc_html_e( 'Fine tune file naming, paste behavior, and local image processing.', 'quickwebp' ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_cleanup',
                                'label'       => __( 'Enable/disable file name cleanup', 'quickwebp' ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_cleanup' ),
                                // 'description' => __( 'Remove special characters from file names.', 'quickwebp' ),
                            ) ); ?>

                            <tr class="quickwebp-cleanup-example-row">
                                <th>
                                    
                                </th>
                                <td>
                                    <div class="quickwebp-cleanup-example">
                                        <p><?php esc_html_e( 'Use a filename with spaces, apostrophes, or accents to preview the cleaned filename.', 'quickwebp' ); ?></p>
                                        <input
                                            type="text"
                                            class="quickwebp-cleanup-example-input"
                                            value="Men's Summer Shoes - Limited Edition 2026.jpg"
                                            aria-label="<?php echo esc_attr__( 'Cleanup filename example input', 'quickwebp' ); ?>"
                                        >
                                        <div class="quickwebp-cleanup-example-grid">
                                            <div class="quickwebp-cleanup-example-item">
                                                <strong><?php esc_html_e( 'Original filename', 'quickwebp' ); ?></strong>
                                                <span class="quickwebp-cleanup-preview-original"></span>
                                            </div>
                                            <div class="quickwebp-cleanup-example-item">
                                                <strong><?php esc_html_e( 'Cleaned filename', 'quickwebp' ); ?></strong>
                                                <span class="quickwebp-cleanup-preview-cleaned"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_paste_image',
                                'label'       => __( 'Enable/disable paste picture directly (beta)', 'quickwebp' ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_paste_image' ),
                                'description' => __( 'With this feature you can paste directly your picture in WordPress media.', 'quickwebp' ),
                            ) ); ?>
                        </table>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-migration">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Migration notice', 'quickwebp' ); ?></h2>
                        <p><?php esc_html_e( 'QuickWebP is now available inside WPMasterToolKit.', 'quickwebp' ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <p><?php esc_html_e( 'QuickWebP is now part of the WPMasterToolKit plugin. You can download it for free on the WordPress repository.', 'quickwebp' ); ?></p>
                        <video autoplay loop muted controls class="quickwebp-migration-video">
                            <source src="<?php echo esc_url( QUICKWEBP_PLUGIN_URL . 'public/assets/video/wpmastertoolkit.mp4' ); ?>" type="video/mp4">
                            <?php esc_html_e( 'Your browser does not support the video tag.', 'quickwebp' ); ?>
                        </video>
                        <?php if ( $wpmtk_is_active ) : ?>
                            <p><strong><?php esc_html_e( 'You can now deactivate QuickWebP and finish the migration.', 'quickwebp' ); ?></strong></p>
                        <?php else : ?>
                            <p>
                                <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=wpmastertoolkit&tab=search&type=term' ) ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'Download WPMasterToolKit', 'quickwebp' ); ?></a>
                                <a href="https://wordpress.org/plugins/wpmastertoolkit/" class="button button-secondary" target="_blank"><?php esc_html_e( 'Download WPMasterToolKit from wordpress.org', 'quickwebp' ); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                </section>

                <div class="quickwebp-submit-wrap">
                    <?php submit_button( __( 'Save settings', 'quickwebp' ), 'primary', 'submit', false ); ?>
                    <p class="quickwebp-submit-note"><?php esc_html_e( 'Your consistency rules are enforced automatically when saving.', 'quickwebp' ); ?></p>
                </div>

                <div class="quickwebp-credits">
                    <p>
                        <?php esc_html_e( 'This plugin is developed by', 'quickwebp' ); ?>
                        <a href="https://webdeclic.com/" target="_blank">Webdeclic</a>.
                        <?php esc_html_e( 'You can support this project here:', 'quickwebp' ); ?>
                        <a href="https://www.buymeacoffee.com/ludwig" target="_blank"><?php esc_html_e( 'Buy me a coffee', 'quickwebp' ); ?></a>.
                    </p>
                    <p>
                        <?php esc_html_e( 'You can show all Webdeclic plugins on', 'quickwebp' ); ?>
                        <a href="https://wordpress.org/plugins/search/webdeclic/" target="_blank">wordpress.org</a>.
                    </p>
                </div>
            </main>

            <aside class="quickwebp-aside">
                <div class="quickwebp-aside-inner">
                    <section class="quickwebp-card">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Live configuration summary', 'quickwebp' ); ?></h2>
                            <p><?php esc_html_e( 'This panel updates instantly while you edit your settings.', 'quickwebp' ); ?></p>
                        </div>
                        <div class="quickwebp-card-body">
                            <ul class="quickwebp-summary-list">
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Conversion', 'quickwebp' ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-conversion-side"><?php echo esc_html( $conversion_enabled ? __( 'Enabled', 'quickwebp' ) : __( 'Disabled', 'quickwebp' ) ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Original images', 'quickwebp' ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-originals-side"><?php echo esc_html( $save_original_enabled ? __( 'Preserved', 'quickwebp' ) : __( 'Replaced', 'quickwebp' ) ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Frontend display', 'quickwebp' ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-display-side"><?php echo esc_html( $display_mode ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Bulk optimization', 'quickwebp' ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-bulk-side"><?php echo esc_html( $save_original_enabled ? __( 'Available', 'quickwebp' ) : __( 'Unavailable', 'quickwebp' ) ); ?></span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section class="quickwebp-card">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Quick navigation', 'quickwebp' ); ?></h2>
                        </div>
                        <div class="quickwebp-card-body quickwebp-quick-links">
                            <?php if ( $show_onboarding ) : ?>
                                <a href="#quickwebp-onboarding"><?php esc_html_e( 'Quick setup assistant', 'quickwebp' ); ?></a>
                            <?php endif; ?>
                            <a href="#quickwebp-conversion"><?php esc_html_e( 'Conversion and delivery', 'quickwebp' ); ?></a>
                            <a href="#quickwebp-resize"><?php esc_html_e( 'Resize', 'quickwebp' ); ?></a>
                            <a href="#quickwebp-seo"><?php esc_html_e( 'SEO metadata automation', 'quickwebp' ); ?></a>
                            <a href="#quickwebp-tools"><?php esc_html_e( 'Other tools', 'quickwebp' ); ?></a>
                            <a href="#quickwebp-migration"><?php esc_html_e( 'Migration notice', 'quickwebp' ); ?></a>
                        </div>
                    </section>

                    <section class="quickwebp-card">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'License', 'quickwebp' ); ?></h2>
                        </div>
                        <div class="quickwebp-card-body quickwebp-license">
                            <div class="quickwebp-license__status">
								<div class="quickwebp-license__status__text"><?php esc_html_e( 'State', 'quickwebp' ); ?>:</div>
								<div class="quickwebp-license__status__icon <?php echo $license_valid ? 'valid' : 'invalid'; ?>"></div>
							</div>

							<div class="quickwebp-license__link">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=quickwebp-manage-license' ) ); ?>" class="<?php echo $license_valid ? 'valid' : 'invalid'; ?>">
									<?php if ( $license_valid ) : ?>
										<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/key-disable.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
										<?php esc_html_e( 'Deactivate the license', 'quickwebp' ); ?>
									<?php else : ?>
										<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/key.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
										<?php esc_html_e( 'Activate the license', 'quickwebp' ); ?>
									<?php endif; ?>
								</a>
							</div>
                        </div>
                    </section>
                </div>
            </aside>
        </div>
    </form>

	<?php if ( ! $license_valid ): ?>
		<div class="quickwebp__pro-popup">
			<div class="quickwebp__pro-popup__overlay"></div>
			<div class="quickwebp__pro-popup__content">
				<div class="quickwebp__pro-popup__content__header">
					<div class="quickwebp__pro-popup__content__header__text">
						<div class="quickwebp__pro-popup__content__header__text__subtitle">
							<div class="quickwebp__pro-popup__content__header__text__subtitle__tag"><?php esc_html_e( 'Pro', 'quickwebp' ); ?></div>
							<div class="quickwebp__pro-popup__content__header__text__subtitle__text"><?php esc_html_e( 'AVIF Format', 'quickwebp' ); ?></div>
						</div>
						<div class="quickwebp__pro-popup__content__header__text__title">
							<?php esc_html_e( 'Go further with', 'quickwebp' ); ?>
							<span class="highlight"> AVIF</span>
						</div>
						<div class="quickwebp__pro-popup__content__header__text__desc">
							<?php esc_html_e( 'The next-generation image format, available with', 'quickwebp' ); ?>
							<span class="highlight">QuickWebP Pro.</span>
						</div>
					</div>

					<div class="quickwebp__pro-popup__content__header__close">
						<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/close.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
					</div>
				</div>
				<div class="quickwebp__pro-popup__content__body">
					<div class="quickwebp__pro-popup__content__body__top">
						<div class="quickwebp__pro-popup__content__body__top__item">
							<div class="quickwebp__pro-popup__content__body__top__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/gauge.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__top__item__text">
								<div class="quickwebp__pro-popup__content__body__top__item__text__title">-50%</div>
								<div class="quickwebp__pro-popup__content__body__top__item__text__desc"><?php esc_html_e( 'Vs jpeg/png on average', 'quickwebp' ); ?></div>
							</div>
						</div>

						<div class="quickwebp__pro-popup__content__body__top__item second">
							<div class="quickwebp__pro-popup__content__body__top__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/chart-line.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__top__item__text">
								<div class="quickwebp__pro-popup__content__body__top__item__text__title">-30%</div>
								<div class="quickwebp__pro-popup__content__body__top__item__text__desc"><?php esc_html_e( 'Vs webp on average', 'quickwebp' ); ?></div>
							</div>
						</div>

						<div class="quickwebp__pro-popup__content__body__top__item third">
							<div class="quickwebp__pro-popup__content__body__top__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/global.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__top__item__text">
								<div class="quickwebp__pro-popup__content__body__top__item__text__title">100%</div>
								<div class="quickwebp__pro-popup__content__body__top__item__text__desc"><?php esc_html_e( 'of modern browsers supported', 'quickwebp' ); ?></div>
							</div>
						</div>
					</div>

					<div class="quickwebp__pro-popup__content__body__middle">
						<div class="quickwebp__pro-popup__content__body__middle__item">
							<div class="quickwebp__pro-popup__content__body__middle__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__middle__item__text">
								<strong><?php echo wp_kses_post(
									// translators: %s is a placeholder for the text "</strong>".
									sprintf( __( 'The best compression available %s images typically half the size of your originals.', 'quickwebp' ), '</strong>' )
								); ?>
							</div>
						</div>

						<div class="quickwebp__pro-popup__content__body__middle__item">
							<div class="quickwebp__pro-popup__content__body__middle__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__middle__item__text">
								<strong><?php echo wp_kses_post(
									// translators: %s is a placeholder for the text "</strong>".
									sprintf( __( 'Sharper images at lower weight %s for faster pages and better Core Web Vitals.', 'quickwebp' ), '</strong>' )
								); ?>
							</div>
						</div>

						<div class="quickwebp__pro-popup__content__body__middle__item">
							<div class="quickwebp__pro-popup__content__body__middle__item__icon">
								<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/check-mark.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
							</div>
							<div class="quickwebp__pro-popup__content__body__middle__item__text">
								<strong><?php echo wp_kses_post(
									// translators: %s is a placeholder for the text "</strong>".
									sprintf( __( 'Automatic fallback %s so older browsers keep receiving a compatible format.', 'quickwebp' ), '</strong>' )
								); ?>
							</div>
						</div>

					</div>

					<div class="quickwebp__pro-popup__content__body__bottom">
						<div class="quickwebp__pro-popup__content__body__bottom__upgrade">
                            <a href="<?php echo esc_url( quickwebp_get_pro_url( 'settings-upgrade-popup' ) ); ?>" class="quickwebp__pro-popup__content__body__bottom__upgrade__link" target="_blank">
								<span class="quickwebp__pro-popup__content__body__bottom__upgrade__link__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/crown.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</span>
								<?php esc_html_e( 'Upgrade to QuickWebP Pro', 'quickwebp' ); ?>
							</a>
						</div>

						<div class="quickwebp__pro-popup__content__body__bottom__license">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=quickwebp-manage-license' ) ); ?>" class="quickwebp__pro-popup__content__body__bottom__license__link">
								<span class="quickwebp__pro-popup__content__body__bottom__license__link__icon">
									<?php echo wp_kses( file_get_contents( QUICKWEBP_PLUGIN_PATH . 'public/assets/svg/shield-check.svg' ), quickwebp_allowed_tags_for_svg_files() ); ?>
								</span>
								<?php esc_html_e( 'I already have a license', 'quickwebp' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const conversionToggles = document.querySelectorAll('input[type="radio"][name="quickwebp_settings_conversion"]');
            const saveOriginalCheckbox = document.querySelector('input[type="checkbox"][name="quickwebp_settings_conversion_save_original[]"]');
            const displayModeInputs = document.querySelectorAll('input[type="radio"][name="quickwebp_settings_conversion_display_webp_mode"]');
            const disabledDisplayModeInput = document.querySelector('input[type="radio"][name="quickwebp_settings_conversion_display_webp_mode"][value="disabled"]');
            const pictureDisplayModeInput = document.querySelector('input[type="radio"][name="quickwebp_settings_conversion_display_webp_mode"][value="picture"]');
            const rewriteDisplayModeInput = document.querySelector('input[type="radio"][name="quickwebp_settings_conversion_display_webp_mode"][value="rewrite"]');
            const bulkRow = document.querySelector('.quickwebp-row-bulk-optimization');
            const guidanceMessage = document.querySelector('.quickwebp-guidance-message');
            const displayWarningRow = document.querySelector('.quickwebp-display-warning-row');
            const displayWarningText = document.querySelector('.quickwebp-display-warning');
            const onboardingButtons = document.querySelectorAll('.quickwebp-onboarding-apply');
            const onboardingFeedback = document.querySelector('.quickwebp-onboarding-feedback');
            const seoExampleInput = document.querySelector('.quickwebp-seo-example-input');
            const seoPreviewTitle = document.querySelector('.quickwebp-seo-preview-title');
            const seoPreviewCaption = document.querySelector('.quickwebp-seo-preview-caption');
            const seoPreviewAlt = document.querySelector('.quickwebp-seo-preview-alt');
            const seoPreviewDescription = document.querySelector('.quickwebp-seo-preview-description');
            const completionToggle = document.querySelector('input[type="checkbox"][name="quickwebp_settings_completion"]');
            const completionOptionInputs = document.querySelectorAll('input[type="checkbox"][name="quickwebp_settings_completion_options[]"]');
            const cleanupToggle = document.querySelector('input[type="checkbox"][name="quickwebp_settings_cleanup"]');
            const cleanupExampleInput = document.querySelector('.quickwebp-cleanup-example-input');
            const cleanupPreviewOriginal = document.querySelector('.quickwebp-cleanup-preview-original');
            const cleanupPreviewCleaned = document.querySelector('.quickwebp-cleanup-preview-cleaned');

            const displayModeRow = displayModeInputs.length ? displayModeInputs[0].closest('tr') : null;
            const disabledDisplayModeLabel = disabledDisplayModeInput ? disabledDisplayModeInput.closest('label') : null;
            const conversionCard = document.querySelector('#quickwebp-conversion');

            const summary = {
                sideConversion: document.querySelector('.quickwebp-summary-conversion-side'),
                sideOriginals: document.querySelector('.quickwebp-summary-originals-side'),
                sideDisplay: document.querySelector('.quickwebp-summary-display-side'),
                sideBulk: document.querySelector('.quickwebp-summary-bulk-side')
            };

            const strings = {
                noOriginals: <?php echo wp_json_encode( __( 'Current mode: originals are replaced on upload. Frontend display mode and bulk optimization are hidden because they are designed for preserved originals.', 'quickwebp' ) ); ?>,
                withOriginals: <?php echo wp_json_encode( __( 'Current mode: originals are preserved. Choose a frontend display strategy and run bulk optimization for existing media.', 'quickwebp' ) ); ?>,
                conversionOff: <?php echo wp_json_encode( __( 'Conversion is disabled. Frontend display mode is forced to Deactivate.', 'quickwebp' ) ); ?>,
                warningNoOriginals: <?php echo wp_json_encode( __( 'Display mode is locked to Deactivate because original images are not preserved.', 'quickwebp' ) ); ?>,
                warningConversionOff: <?php echo wp_json_encode( __( 'Display mode is locked to Deactivate because image conversion is disabled.', 'quickwebp' ) ); ?>,
                appliedNewSite: <?php echo wp_json_encode( __( 'Preset applied: New site profile. Originals are replaced and display mode is set to Deactivate.', 'quickwebp' ) ); ?>,
                appliedExistingSite: <?php echo wp_json_encode( __( 'Preset applied: Existing site profile. Originals are preserved and rewrite mode is selected.', 'quickwebp' ) ); ?>,
                profileNew: <?php echo wp_json_encode( __( 'New site profile', 'quickwebp' ) ); ?>,
                profileExisting: <?php echo wp_json_encode( __( 'Existing site profile', 'quickwebp' ) ); ?>,
                profileCustom: <?php echo wp_json_encode( __( 'Custom profile', 'quickwebp' ) ); ?>,
                enabled: <?php echo wp_json_encode( __( 'Enabled', 'quickwebp' ) ); ?>,
                disabled: <?php echo wp_json_encode( __( 'Disabled', 'quickwebp' ) ); ?>,
                preserved: <?php echo wp_json_encode( __( 'Preserved', 'quickwebp' ) ); ?>,
                replaced: <?php echo wp_json_encode( __( 'Replaced', 'quickwebp' ) ); ?>,
                available: <?php echo wp_json_encode( __( 'Available', 'quickwebp' ) ); ?>,
                unavailable: <?php echo wp_json_encode( __( 'Unavailable', 'quickwebp' ) ); ?>,
                disabledPreview: <?php echo wp_json_encode( __( 'Disabled in current settings', 'quickwebp' ) ); ?>,
                cleanupDisabledPreview: <?php echo wp_json_encode( __( 'Cleanup disabled in current settings', 'quickwebp' ) ); ?>,
            };

            function setDisplayMode(value) {
                displayModeInputs.forEach((input) => {
                    input.checked = input.value === value;
                });
            }

            function getSelectedDisplayMode() {
                const selected = Array.from(displayModeInputs).find((input) => input.checked);
                return selected ? selected.value : 'disabled';
            }

            function isSaveOriginalEnabled() {
                return !!saveOriginalCheckbox && saveOriginalCheckbox.checked;
            }

            function isConversionEnabled() {
				let conversionToggle = document.querySelector('input[type="radio"][name="quickwebp_settings_conversion"]:checked');

                return !!conversionToggle && conversionToggle.value !== '0';
            }

            function getProfile(hasOriginals, conversionEnabled) {
                if (!conversionEnabled) {
                    return strings.profileCustom;
                }

                return hasOriginals ? strings.profileExisting : strings.profileNew;
            }

            function updateSummary() {
                const hasOriginals = isSaveOriginalEnabled();
                const conversionEnabled = isConversionEnabled();
                const displayMode = getSelectedDisplayMode();
                const profile = getProfile(hasOriginals, conversionEnabled);

                if (summary.sideConversion) {
                    summary.sideConversion.textContent = conversionEnabled ? strings.enabled : strings.disabled;
                }

                if (summary.sideOriginals) {
                    summary.sideOriginals.textContent = hasOriginals ? strings.preserved : strings.replaced;
                }

                if (summary.sideDisplay) {
                    summary.sideDisplay.textContent = displayMode;
                }

                if (summary.sideBulk) {
                    summary.sideBulk.textContent = hasOriginals && conversionEnabled ? strings.available : strings.unavailable;
                }
            }

            function updateUiConsistency() {
                const hasOriginals = isSaveOriginalEnabled();
                const conversionEnabled = isConversionEnabled();
                const forceDisabled = !hasOriginals || !conversionEnabled;

                if (displayModeRow) {
                    displayModeRow.style.display = hasOriginals && conversionEnabled ? '' : 'none';
                }

                if (bulkRow) {
                    bulkRow.style.display = hasOriginals && conversionEnabled ? '' : 'none';
                }

                if (guidanceMessage) {
                    if (!conversionEnabled) {
                        guidanceMessage.textContent = strings.conversionOff;
                    } else {
                        guidanceMessage.textContent = hasOriginals ? strings.withOriginals : strings.noOriginals;
                    }
                }

                if (displayWarningRow && displayWarningText) {
                    displayWarningRow.style.display = forceDisabled ? '' : 'none';

                    if (!conversionEnabled) {
                        displayWarningText.textContent = strings.warningConversionOff;
                    } else {
                        displayWarningText.textContent = strings.warningNoOriginals;
                    }
                }

                if (disabledDisplayModeLabel) {
                    disabledDisplayModeLabel.style.display = hasOriginals && conversionEnabled ? 'none' : '';
                }

                if (disabledDisplayModeInput) {
                    disabledDisplayModeInput.disabled = hasOriginals && conversionEnabled;
                }

                if (forceDisabled) {
                    setDisplayMode('disabled');
                    updateSummary();
                    return;
                }

                if (disabledDisplayModeInput && disabledDisplayModeInput.checked) {
                    if (pictureDisplayModeInput) {
                        pictureDisplayModeInput.checked = true;
                    } else if (rewriteDisplayModeInput) {
                        rewriteDisplayModeInput.checked = true;
                    }
                }

                updateSummary();
            }

            function extractBaseFilename(value) {
                if (!value) {
                    return 'example image';
                }

                const base = value.replace(/\.[^/.]+$/, '');
                const normalized = base
                    .replace(/\s+/g, ' ')
                    .trim();

                return normalized || 'example image';
            }

            function updateSeoLiveExample() {
                if (!seoExampleInput || !seoPreviewTitle || !seoPreviewCaption || !seoPreviewAlt || !seoPreviewDescription) {
                    return;
                }

                const optionsMap = {
                    title: {
                        checked: !!document.querySelector('input[type="checkbox"][name="quickwebp_settings_completion_options[]"][value="title"]:checked'),
                        preview: seoPreviewTitle
                    },
                    caption: {
                        checked: !!document.querySelector('input[type="checkbox"][name="quickwebp_settings_completion_options[]"][value="caption"]:checked'),
                        preview: seoPreviewCaption
                    },
                    alt: {
                        checked: !!document.querySelector('input[type="checkbox"][name="quickwebp_settings_completion_options[]"][value="alt"]:checked'),
                        preview: seoPreviewAlt
                    },
                    description: {
                        checked: !!document.querySelector('input[type="checkbox"][name="quickwebp_settings_completion_options[]"][value="description"]:checked'),
                        preview: seoPreviewDescription
                    }
                };

                const completionEnabled = !!completionToggle && completionToggle.checked;
                const baseFilename = extractBaseFilename(seoExampleInput.value);

                optionsMap.title.preview.textContent = baseFilename;
                optionsMap.caption.preview.textContent = baseFilename;
                optionsMap.alt.preview.textContent = baseFilename;
                optionsMap.description.preview.textContent = baseFilename;

                Object.keys(optionsMap).forEach((key) => {
                    const item = optionsMap[key];
                    const container = item.preview.closest('.quickwebp-seo-example-item');
                    const isEnabled = completionEnabled && item.checked;

                    if (!isEnabled) {
                        item.preview.textContent = strings.disabledPreview;
                    }

                    if (container) {
                        container.classList.toggle('is-disabled', !isEnabled);
                    }
                });
            }

            function sanitizeFilenamePreview(value) {
                const inputValue = (value || '').trim();
                if (!inputValue) {
                    return 'example-image.jpg';
                }

                const lastDot = inputValue.lastIndexOf('.');
                const hasExtension = lastDot > 0 && lastDot < inputValue.length - 1;
                const extension = hasExtension ? inputValue.slice(lastDot + 1).toLowerCase() : '';
                const basename = hasExtension ? inputValue.slice(0, lastDot) : inputValue;

                let normalized = basename;
                if (normalized.normalize) {
                    normalized = normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                }

                normalized = normalized
                    .replace(/[’']/g, '-')
                    .replace(/[^A-Za-z0-9\s-]/g, '')
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');

                if (!normalized) {
                    normalized = 'image';
                }

                return extension ? normalized + '.' + extension : normalized;
            }

            function updateCleanupLiveExample() {
                if (!cleanupExampleInput || !cleanupPreviewOriginal || !cleanupPreviewCleaned) {
                    return;
                }

                const original = cleanupExampleInput.value || '';
                const cleaned = sanitizeFilenamePreview(original);
                const isCleanupEnabled = !!cleanupToggle && cleanupToggle.checked;

                cleanupPreviewOriginal.textContent = original || 'example image.jpg';

                const cleanedContainer = cleanupPreviewCleaned.closest('.quickwebp-cleanup-example-item');
                if (!isCleanupEnabled) {
                    cleanupPreviewCleaned.textContent = strings.cleanupDisabledPreview;
                    if (cleanedContainer) {
                        cleanedContainer.classList.add('is-disabled');
                    }
                    return;
                }

                cleanupPreviewCleaned.textContent = cleaned;
                if (cleanedContainer) {
                    cleanedContainer.classList.remove('is-disabled');
                }
            }

            onboardingButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const profile = this.getAttribute('data-profile');

					let conversionToggle = document.querySelector('input[type="radio"][name="quickwebp_settings_conversion"][value="1"]');
                    if (conversionToggle) {
                        conversionToggle.checked = true;
                        conversionToggle.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    if (!saveOriginalCheckbox) {
                        return;
                    }

                    if (profile === 'new-site') {
                        saveOriginalCheckbox.checked = false;
                        setDisplayMode('disabled');

                        if (onboardingFeedback) {
                            onboardingFeedback.textContent = strings.appliedNewSite;
                        }
                    }

                    if (profile === 'existing-site') {
                        saveOriginalCheckbox.checked = true;
                        if (rewriteDisplayModeInput) {
                            rewriteDisplayModeInput.checked = true;
                        } else {
                            setDisplayMode('picture');
                        }

                        if (onboardingFeedback) {
                            onboardingFeedback.textContent = strings.appliedExistingSite;
                        }
                    }

                    updateUiConsistency();

                    if (conversionCard) {
                        conversionCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            if (saveOriginalCheckbox) {
                saveOriginalCheckbox.addEventListener('change', updateUiConsistency);
            }

			conversionToggles.forEach((toggle) => {
				toggle.addEventListener('change', updateUiConsistency);
			});

            displayModeInputs.forEach((input) => {
                input.addEventListener('change', updateSummary);
            });

            if (seoExampleInput) {
                seoExampleInput.addEventListener('input', updateSeoLiveExample);
                updateSeoLiveExample();
            }

            if (cleanupExampleInput) {
                cleanupExampleInput.addEventListener('input', updateCleanupLiveExample);
                updateCleanupLiveExample();
            }

            if (completionToggle) {
                completionToggle.addEventListener('change', updateSeoLiveExample);
            }

            if (cleanupToggle) {
                cleanupToggle.addEventListener('change', updateCleanupLiveExample);
            }

            completionOptionInputs.forEach((input) => {
                input.addEventListener('change', updateSeoLiveExample);
            });

            document.querySelectorAll('.quickwebp-quick-links a[href^="#"]').forEach((anchor) => {
                anchor.addEventListener('click', function (event) {
                    const target = document.querySelector(this.getAttribute('href'));
                    if (!target) {
                        return;
                    }

                    event.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            updateUiConsistency();
        });
    </script>
</div>
