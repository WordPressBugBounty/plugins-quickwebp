<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The admin settings of the plugin.
 * @since      1.0.0
 */
$wpmtk_is_active = in_array( 'wpmastertoolkit/wp-mastertoolkit.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

if ( isset( $_POST['quickwebp_settings_conversion'] ) ) {
    update_option( 'quickwebp_settings_onboarding_completed', '1' );
}

$show_onboarding = '1' !== (string) get_option( 'quickwebp_settings_onboarding_completed', '0' );

$conversion_enabled     = '1' === (string) get_option( 'quickwebp_settings_conversion', quickwebp_settings_default( 'quickwebp_settings_conversion' ) );
$save_original          = get_option( 'quickwebp_settings_conversion_save_original', quickwebp_settings_default( 'quickwebp_settings_conversion_save_original' ) );
$save_original          = is_array( $save_original ) ? $save_original : array();
$save_original_enabled  = in_array( 'checked', $save_original, true );
$display_mode           = get_option( 'quickwebp_settings_conversion_display_webp_mode', quickwebp_settings_default( 'quickwebp_settings_conversion_display_webp_mode' ) );

$profile_label = __( 'Custom profile', QUICKWEBP_TEXT_DOMAIN );
if ( $conversion_enabled && ! $save_original_enabled ) {
    $profile_label = __( 'New site profile', QUICKWEBP_TEXT_DOMAIN );
}
if ( $conversion_enabled && $save_original_enabled ) {
    $profile_label = __( 'Existing site profile', QUICKWEBP_TEXT_DOMAIN );
}
?>
<div class="quickwebp-settings-v2" data-current-profile="<?php echo esc_attr( $profile_label ); ?>">
    <style>
        .quickwebp-settings-v2 {
            --quickwebp-surface: #ffffff;
            --quickwebp-text: #111827;
            --quickwebp-muted: #4b5563;
            --quickwebp-border: #d6deeb;
            --quickwebp-accent: #0f766e;
            --quickwebp-accent-soft: #e6f7f6;
            --quickwebp-info-soft: #eef4ff;
            --quickwebp-warning-soft: #fff8e8;
            max-width: 1320px;
            margin-top: 14px;
            color: var(--quickwebp-text);
        }

        .quickwebp-hero {
            background: linear-gradient(120deg, #e9f8f6 0%, #eef4ff 45%, #ffffff 100%);
            border: 1px solid var(--quickwebp-border);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .quickwebp-hero h1 {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.2;
        }

        .quickwebp-hero p {
            margin: 0;
            color: var(--quickwebp-muted);
            font-size: 14px;
        }

        .quickwebp-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 16px;
            align-items: start;
        }

        .quickwebp-main,
        .quickwebp-aside {
            min-width: 0;
        }

        .quickwebp-card {
            background: var(--quickwebp-surface);
            border: 1px solid var(--quickwebp-border);
            border-radius: 14px;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .quickwebp-card-head {
            padding: 14px 16px;
            border-bottom: 1px solid #ebeff7;
            background: #fbfdff;
        }

        .quickwebp-card-head h2 {
            margin: 0;
            font-size: 16px;
            line-height: 1.3;
        }

        .quickwebp-card-head p {
            margin: 4px 0 0;
            color: var(--quickwebp-muted);
            font-size: 13px;
        }

        .quickwebp-card-body {
            padding: 10px 16px 4px;
        }

        .quickwebp-card-body .form-table {
            margin-top: 0;
        }

        .quickwebp-card-body .form-table th {
            width: 300px;
        }

        .quickwebp-card-body .form-table td,
        .quickwebp-card-body .form-table th {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .quickwebp-onboarding-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin: 10px 0 0;
        }

        .quickwebp-onboarding-option {
            border: 1px solid var(--quickwebp-border);
            border-radius: 10px;
            padding: 12px;
            background: #fff;
            text-align: left;
            cursor: pointer;
        }

        .quickwebp-onboarding-option strong {
            display: block;
            margin-bottom: 3px;
        }

        .quickwebp-onboarding-option span {
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-onboarding-option:hover,
        .quickwebp-onboarding-option:focus {
            border-color: #88bbb7;
            background: #f8fffe;
            outline: none;
        }

        .quickwebp-onboarding-feedback {
            margin: 8px 0 0;
            color: #0f5b56;
            font-weight: 600;
        }

        .quickwebp-guidance-box {
            margin: 6px 0 0;
            padding: 10px;
            border-radius: 8px;
            background: var(--quickwebp-info-soft);
            border: 1px solid #cad7f7;
        }

        .quickwebp-guidance-message {
            margin: 0;
            color: #1f3a73;
            font-size: 12px;
        }

        .quickwebp-display-warning {
            margin: 8px 0 0;
            padding: 8px 10px;
            border-radius: 8px;
            background: var(--quickwebp-warning-soft);
            border: 1px solid #f6dc9e;
            color: #8a5a00;
            font-size: 12px;
            font-weight: 600;
        }

        .quickwebp-aside-inner {
            position: sticky;
            top: 36px;
        }

        .quickwebp-summary-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .quickwebp-summary-list li {
            border: 1px solid #e7edf8;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 8px;
            background: #fff;
        }

        .quickwebp-summary-label {
            display: block;
            margin-bottom: 2px;
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-summary-value {
            display: block;
            font-size: 13px;
            font-weight: 700;
        }

        .quickwebp-quick-links {
            display: grid;
            gap: 6px;
        }

        .quickwebp-quick-links a {
            text-decoration: none;
            color: #0f5b56;
            font-weight: 600;
            font-size: 13px;
        }

        .quickwebp-migration-video {
            max-width: 100%;
            border-radius: 10px;
            border: 1px solid #d9e4fb;
            margin-top: 8px;
        }

        .quickwebp-submit-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
            padding: 14px 16px;
            border: 1px solid var(--quickwebp-border);
            border-radius: 12px;
            background: #fff;
        }

        .quickwebp-submit-wrap .submit {
            margin: 0;
            padding: 0;
        }

        .quickwebp-submit-note {
            margin: 0;
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-credits {
            margin-top: 8px;
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-seo-example {
            margin-top: 10px;
            border: 1px solid #dbe6fb;
            border-radius: 10px;
            background: #f8fbff;
            padding: 12px;
        }

        .quickwebp-seo-example h3 {
            margin: 0 0 4px;
            font-size: 13px;
        }

        .quickwebp-seo-example p {
            margin: 0 0 8px;
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-seo-example-input {
            width: 100%;
            margin-bottom: 8px;
        }

        .quickwebp-seo-example-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .quickwebp-seo-example-item {
            background: #fff;
            border: 1px solid #e3ebfb;
            border-radius: 8px;
            padding: 8px;
        }

        .quickwebp-seo-example-item.is-disabled {
            opacity: 0.45;
            background: #f3f5f9;
            border-color: #dde3ef;
        }

        .quickwebp-seo-example-item strong {
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
            color: #4f648f;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .quickwebp-seo-example-item span {
            display: block;
            font-size: 12px;
            color: #1f2937;
            word-break: break-word;
        }

        .quickwebp-cleanup-example {
            margin-top: 10px;
            border: 1px solid #d9efdb;
            border-radius: 10px;
            background: #f8fff8;
            padding: 12px;
        }

        .quickwebp-cleanup-example h3 {
            margin: 0 0 4px;
            font-size: 13px;
        }

        .quickwebp-cleanup-example p {
            margin: 0 0 8px;
            color: var(--quickwebp-muted);
            font-size: 12px;
        }

        .quickwebp-cleanup-example-input {
            width: 100%;
            margin-bottom: 8px;
        }

        .quickwebp-cleanup-example-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .quickwebp-cleanup-example-item {
            background: #fff;
            border: 1px solid #dceede;
            border-radius: 8px;
            padding: 8px;
        }

        .quickwebp-cleanup-example-item strong {
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
            color: #3f6f44;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .quickwebp-cleanup-example-item span {
            display: block;
            font-size: 12px;
            color: #1f2937;
            word-break: break-word;
        }

        .quickwebp-cleanup-example-item.is-disabled {
            opacity: 0.45;
            background: #f3f5f9;
            border-color: #dde3ef;
        }

        @media (max-width: 1190px) {
            .quickwebp-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .quickwebp-aside-inner {
                position: static;
            }

            .quickwebp-onboarding-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .quickwebp-seo-example-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .quickwebp-cleanup-example-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <header class="quickwebp-hero">
        <h1><?php esc_html_e( 'QuickWebP Settings', QUICKWEBP_TEXT_DOMAIN ); ?></h1>
        <p><?php esc_html_e( 'Configure your image workflow with coherent presets, guarded options, and an optimized media delivery strategy.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
    </header>

    <form action="" method="post" class="quickwebp-settings-form">
        <div class="quickwebp-layout">
            <main class="quickwebp-main">
                <?php if ( $show_onboarding ) : ?>
                    <section class="quickwebp-card" id="quickwebp-onboarding">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Quick setup assistant', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                            <p><?php esc_html_e( 'Start from a recommended profile and refine only what you need.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                        </div>
                        <div class="quickwebp-card-body">
                            <div class="quickwebp-onboarding-grid">
                                <button type="button" class="quickwebp-onboarding-option quickwebp-onboarding-apply" data-profile="new-site">
                                    <strong><?php esc_html_e( 'New site with few media', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span><?php esc_html_e( 'Replace originals on upload for the simplest and fastest workflow.', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                </button>
                                <button type="button" class="quickwebp-onboarding-option quickwebp-onboarding-apply" data-profile="existing-site">
                                    <strong><?php esc_html_e( 'Existing site with many media', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span><?php esc_html_e( 'Keep originals, enable rewrite delivery, and run bulk optimization.', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                </button>
                            </div>
                            <p class="quickwebp-onboarding-feedback" aria-live="polite"></p>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="quickwebp-card" id="quickwebp-conversion">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Conversion and Delivery', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        <p><?php esc_html_e( 'Define how images are converted, preserved, and delivered on frontend pages.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">

                            <?php $this->render_component( array(
                                'type'      => 'toggle',
                                'name'      => 'quickwebp_settings_conversion',
                                'label'     => __( 'Enable/disable image conversion to WEBP format', QUICKWEBP_TEXT_DOMAIN ),
                                'default'   => quickwebp_settings_default( 'quickwebp_settings_conversion' ),
                                'classes'   => 'toggle-with-children',
                            ) ); ?>

                            <tbody class="form-table children">

                                <?php $this->render_component( array(
                                    'type'        => 'range-slider',
                                    'min'         => 0,
                                    'max'         => 100,
                                    'step'        => 1,
                                    'unit'        => '%',
                                    'name'        => 'quickwebp_settings_conversion_quality',
                                    'label'       => __( 'Quality', QUICKWEBP_TEXT_DOMAIN ),
                                    'default'     => quickwebp_settings_default( 'quickwebp_settings_conversion_quality' )
                                ) ); ?>

                                <?php $this->render_component( array(
                                    'type'        => 'range-slider',
                                    'min'         => 0,
                                    'max'         => 100,
                                    'step'        => 1,
                                    'unit'        => '%',
                                    'name'        => 'quickwebp_settings_conversion_sharpen',
                                    'label'       => __( 'Sharpen', QUICKWEBP_TEXT_DOMAIN ),
                                    'default'     => quickwebp_settings_default( 'quickwebp_settings_conversion_sharpen' )
                                ) ); ?>

                                <?php $this->render_component( array(
                                    'type'      => 'checkbox',
                                    'name'      => 'quickwebp_settings_conversion_ignore_webp',
                                    'label'     => __( 'Do not compress images already in WebP', QUICKWEBP_TEXT_DOMAIN ),
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
                                    'label'     => __( 'Save original images', QUICKWEBP_TEXT_DOMAIN ),
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
                                        <label><?php esc_html_e( 'Consistency guidance', QUICKWEBP_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <div class="quickwebp-guidance-box">
                                            <p class="quickwebp-guidance-message" aria-live="polite"></p>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label><?php esc_html_e( 'Before saving, test your configuration with preview mode.', QUICKWEBP_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <?php include QUICKWEBP_PLUGIN_PATH . 'admin/templates/popup-settings-preview.php'; ?>
                                    </td>
                                </tr>

                                <?php
                                    $description_for_nginx = $is_nginx ? __( "If you choose to use rewrite rules, the file conf/quickwebp.conf will be created and must be included into the server's configuration file (then restart the server).", QUICKWEBP_TEXT_DOMAIN ) : '';

                                    $this->render_component( array(
                                        'type'          => 'radio',
                                        'name'          => 'quickwebp_settings_conversion_display_webp_mode',
                                        'label'         => __( 'Display images in WebP format on the site', QUICKWEBP_TEXT_DOMAIN ),
                                        'description'   => sprintf( __( 'If activated, this option allows to deliver optimized images in bulk via QuickWebP in WebP format (useless for images converted to import). %s', QUICKWEBP_TEXT_DOMAIN ), $description_for_nginx ),
                                        'default'       => quickwebp_settings_default( 'quickwebp_settings_conversion_display_webp_mode' ),
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
                                    ) );
                                ?>

                                <tr class="quickwebp-display-warning-row" style="display:none;">
                                    <th>
                                        <label><?php esc_html_e( 'Why this is locked', QUICKWEBP_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <p class="quickwebp-display-warning"></p>
                                    </td>
                                </tr>

                                <tr class="quickwebp-row-bulk-optimization">
                                    <th>
                                        <label><?php esc_html_e( 'Bulk optimization', QUICKWEBP_TEXT_DOMAIN ); ?></label>
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
                        <h2><?php esc_html_e( 'Resize', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        <p><?php esc_html_e( 'Keep your uploads under control to reduce bandwidth and processing costs.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_resize',
                                'label'       => __( 'Enable/disable image resizing', QUICKWEBP_TEXT_DOMAIN ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_resize' ),
                                'classes'     => 'toggle-with-children',
                                'description' => __( 'By default, WordPress limits the maximum width of uploaded images to 2560 pixels.', QUICKWEBP_TEXT_DOMAIN ),
                            ) ); ?>

                            <tbody class="form-table children">
                                <?php $this->render_component( array(
                                    'type'        => 'number',
                                    'name'        => 'quickwebp_settings_resize_value',
                                    'label'       => __( 'Max size', QUICKWEBP_TEXT_DOMAIN ),
                                    'default'     => quickwebp_settings_default( 'quickwebp_settings_resize_value' )
                                ) ); ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-seo">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'SEO metadata automation', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        <p><?php esc_html_e( 'Auto-fill media fields from image names for a faster editorial workflow.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_completion',
                                'label'       => __( 'Enable/disable smart media completion for SEO', QUICKWEBP_TEXT_DOMAIN ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_completion' ),
                                'classes'     => 'toggle-with-children',
                                'description' => __( 'This feature will automatically complete the media information (title, caption, alt text, description) from the image name.', QUICKWEBP_TEXT_DOMAIN ),
                            ) ); ?>

                            <tbody class="form-table children">
                                <?php $this->render_component( array(
                                    'type'      => 'checkbox',
                                    'name'      => 'quickwebp_settings_completion_options',
                                    'default'   => quickwebp_settings_default( 'quickwebp_settings_completion_options' ),
                                    'options'   => array(
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
                                ) ); ?>
                            </tbody>
                        </table>

                        <div class="quickwebp-seo-example">
                            <h3><?php esc_html_e( 'Live example', QUICKWEBP_TEXT_DOMAIN ); ?></h3>
                            <p><?php esc_html_e( 'Use a descriptive filename once (including spaces or apostrophes), and metadata is auto-filled from that original filename without the extension.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                            <input
                                type="text"
                                class="quickwebp-seo-example-input"
                                value="Men's Summer Shoes - Limited Edition 2026.jpg"
                                aria-label="<?php echo esc_attr__( 'SEO metadata example filename', QUICKWEBP_TEXT_DOMAIN ); ?>"
                            >

                            <div class="quickwebp-seo-example-grid">
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Title', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span class="quickwebp-seo-preview-title"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Caption', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span class="quickwebp-seo-preview-caption"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Alt text', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span class="quickwebp-seo-preview-alt"></span>
                                </div>
                                <div class="quickwebp-seo-example-item">
                                    <strong><?php esc_html_e( 'Description', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                    <span class="quickwebp-seo-preview-description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-tools">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Other tools', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        <p><?php esc_html_e( 'Fine tune file naming, paste behavior, and backend image library.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <table class="form-table">
                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_cleanup',
                                'label'       => __( 'Enable/disable file name cleanup', QUICKWEBP_TEXT_DOMAIN ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_cleanup' ),
                                // 'description' => __( 'Remove special characters from file names.', QUICKWEBP_TEXT_DOMAIN ),
                            ) ); ?>

                            <tr class="quickwebp-cleanup-example-row">
                                <th>
                                    
                                </th>
                                <td>
                                    <div class="quickwebp-cleanup-example">
                                        <p><?php esc_html_e( 'Use a filename with spaces, apostrophes, or accents to preview the cleaned filename.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                                        <input
                                            type="text"
                                            class="quickwebp-cleanup-example-input"
                                            value="Men's Summer Shoes - Limited Edition 2026.jpg"
                                            aria-label="<?php echo esc_attr__( 'Cleanup filename example input', QUICKWEBP_TEXT_DOMAIN ); ?>"
                                        >
                                        <div class="quickwebp-cleanup-example-grid">
                                            <div class="quickwebp-cleanup-example-item">
                                                <strong><?php esc_html_e( 'Original filename', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                                <span class="quickwebp-cleanup-preview-original"></span>
                                            </div>
                                            <div class="quickwebp-cleanup-example-item">
                                                <strong><?php esc_html_e( 'Cleaned filename', QUICKWEBP_TEXT_DOMAIN ); ?></strong>
                                                <span class="quickwebp-cleanup-preview-cleaned"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <?php $this->render_component( array(
                                'type'        => 'toggle',
                                'name'        => 'quickwebp_settings_paste_image',
                                'label'       => __( 'Enable/disable paste picture directly (beta)', QUICKWEBP_TEXT_DOMAIN ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_paste_image' ),
                                'description' => __( 'With this feature you can paste directly your picture in WordPress media.', QUICKWEBP_TEXT_DOMAIN ),
                            ) ); ?>

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
                                'label'       => __( 'Library to use', QUICKWEBP_TEXT_DOMAIN ),
                                'default'     => quickwebp_settings_default( 'quickwebp_settings_library' ),
                                'description' => __( 'We use the GD library as the default option. However, if the GD library is not available, we will use Imagick instead.', QUICKWEBP_TEXT_DOMAIN )
                            ) ); ?>
                        </table>
                    </div>
                </section>

                <section class="quickwebp-card" id="quickwebp-migration">
                    <div class="quickwebp-card-head">
                        <h2><?php esc_html_e( 'Migration notice', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        <p><?php esc_html_e( 'QuickWebP is now available inside WPMasterToolKit.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                    </div>
                    <div class="quickwebp-card-body">
                        <p><?php esc_html_e( 'QuickWebP is now part of the WPMasterToolKit plugin. You can download it for free on the WordPress repository.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                        <video autoplay loop muted controls class="quickwebp-migration-video">
                            <source src="<?php echo esc_url( QUICKWEBP_PLUGIN_URL . 'public/assets/video/wpmastertoolkit.mp4' ); ?>" type="video/mp4">
                            <?php esc_html_e( 'Your browser does not support the video tag.', QUICKWEBP_TEXT_DOMAIN ); ?>
                        </video>
                        <?php if ( $wpmtk_is_active ) : ?>
                            <p><strong><?php esc_html_e( 'You can now deactivate QuickWebP and finish the migration.', QUICKWEBP_TEXT_DOMAIN ); ?></strong></p>
                        <?php else : ?>
                            <p>
                                <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=wpmastertoolkit&tab=search&type=term' ) ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'Download WPMasterToolKit', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                                <a href="https://wordpress.org/plugins/wpmastertoolkit/" class="button button-secondary" target="_blank"><?php esc_html_e( 'Download WPMasterToolKit from wordpress.org', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                </section>

                <div class="quickwebp-submit-wrap">
                    <?php submit_button( __( 'Save settings', QUICKWEBP_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
                    <p class="quickwebp-submit-note"><?php esc_html_e( 'Your consistency rules are enforced automatically when saving.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                </div>

                <div class="quickwebp-credits">
                    <p>
                        <?php esc_html_e( 'This plugin is developed by', QUICKWEBP_TEXT_DOMAIN ); ?>
                        <a href="https://webdeclic.com/" target="_blank">Webdeclic</a>.
                        <?php esc_html_e( 'You can support this project here:', QUICKWEBP_TEXT_DOMAIN ); ?>
                        <a href="https://www.buymeacoffee.com/ludwig" target="_blank"><?php esc_html_e( 'Buy me a coffee', QUICKWEBP_TEXT_DOMAIN ); ?></a>.
                    </p>
                    <p>
                        <?php esc_html_e( 'You can show all Webdeclic plugins on', QUICKWEBP_TEXT_DOMAIN ); ?>
                        <a href="https://wordpress.org/plugins/search/webdeclic/" target="_blank">wordpress.org</a>.
                    </p>
                </div>
            </main>

            <aside class="quickwebp-aside">
                <div class="quickwebp-aside-inner">
                    <section class="quickwebp-card">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Live configuration summary', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                            <p><?php esc_html_e( 'This panel updates instantly while you edit your settings.', QUICKWEBP_TEXT_DOMAIN ); ?></p>
                        </div>
                        <div class="quickwebp-card-body">
                            <ul class="quickwebp-summary-list">
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Conversion', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-conversion-side"><?php echo esc_html( $conversion_enabled ? __( 'Enabled', QUICKWEBP_TEXT_DOMAIN ) : __( 'Disabled', QUICKWEBP_TEXT_DOMAIN ) ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Original images', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-originals-side"><?php echo esc_html( $save_original_enabled ? __( 'Preserved', QUICKWEBP_TEXT_DOMAIN ) : __( 'Replaced', QUICKWEBP_TEXT_DOMAIN ) ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Frontend display', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-display-side"><?php echo esc_html( $display_mode ); ?></span>
                                </li>
                                <li>
                                    <span class="quickwebp-summary-label"><?php esc_html_e( 'Bulk optimization', QUICKWEBP_TEXT_DOMAIN ); ?></span>
                                    <span class="quickwebp-summary-value quickwebp-summary-bulk-side"><?php echo esc_html( $save_original_enabled ? __( 'Available', QUICKWEBP_TEXT_DOMAIN ) : __( 'Unavailable', QUICKWEBP_TEXT_DOMAIN ) ); ?></span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section class="quickwebp-card">
                        <div class="quickwebp-card-head">
                            <h2><?php esc_html_e( 'Quick navigation', QUICKWEBP_TEXT_DOMAIN ); ?></h2>
                        </div>
                        <div class="quickwebp-card-body quickwebp-quick-links">
                            <?php if ( $show_onboarding ) : ?>
                                <a href="#quickwebp-onboarding"><?php esc_html_e( 'Quick setup assistant', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            <?php endif; ?>
                            <a href="#quickwebp-conversion"><?php esc_html_e( 'Conversion and delivery', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            <a href="#quickwebp-resize"><?php esc_html_e( 'Resize', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            <a href="#quickwebp-seo"><?php esc_html_e( 'SEO metadata automation', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            <a href="#quickwebp-tools"><?php esc_html_e( 'Other tools', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                            <a href="#quickwebp-migration"><?php esc_html_e( 'Migration notice', QUICKWEBP_TEXT_DOMAIN ); ?></a>
                        </div>
                    </section>
                </div>
            </aside>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const conversionToggle = document.querySelector('input[type="checkbox"][name="quickwebp_settings_conversion"]');
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
                noOriginals: <?php echo wp_json_encode( __( 'Current mode: originals are replaced on upload. Frontend display mode and bulk optimization are hidden because they are designed for preserved originals.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                withOriginals: <?php echo wp_json_encode( __( 'Current mode: originals are preserved. Choose a frontend display strategy and run bulk optimization for existing media.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                conversionOff: <?php echo wp_json_encode( __( 'Conversion is disabled. Frontend display mode is forced to Deactivate.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                warningNoOriginals: <?php echo wp_json_encode( __( 'Display mode is locked to Deactivate because original images are not preserved.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                warningConversionOff: <?php echo wp_json_encode( __( 'Display mode is locked to Deactivate because image conversion is disabled.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                appliedNewSite: <?php echo wp_json_encode( __( 'Preset applied: New site profile. Originals are replaced and display mode is set to Deactivate.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                appliedExistingSite: <?php echo wp_json_encode( __( 'Preset applied: Existing site profile. Originals are preserved and rewrite mode is selected.', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                profileNew: <?php echo wp_json_encode( __( 'New site profile', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                profileExisting: <?php echo wp_json_encode( __( 'Existing site profile', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                profileCustom: <?php echo wp_json_encode( __( 'Custom profile', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                enabled: <?php echo wp_json_encode( __( 'Enabled', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                disabled: <?php echo wp_json_encode( __( 'Disabled', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                preserved: <?php echo wp_json_encode( __( 'Preserved', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                replaced: <?php echo wp_json_encode( __( 'Replaced', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                available: <?php echo wp_json_encode( __( 'Available', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                unavailable: <?php echo wp_json_encode( __( 'Unavailable', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                disabledPreview: <?php echo wp_json_encode( __( 'Disabled in current settings', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
                cleanupDisabledPreview: <?php echo wp_json_encode( __( 'Cleanup disabled in current settings', QUICKWEBP_TEXT_DOMAIN ) ); ?>,
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
                return !!conversionToggle && conversionToggle.checked;
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

            if (conversionToggle) {
                conversionToggle.addEventListener('change', updateUiConsistency);
            }

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