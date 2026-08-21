<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The global functions for this plugin
 * 
 * @since    1.0.0
 */


/**
 * Sanitize a string
 */
function quickwebp_sanitize_name( $name ) {
    
    $extension      = pathinfo( $name, PATHINFO_EXTENSION );
    $name           = pathinfo( $name, PATHINFO_FILENAME );
    $name           = mb_convert_encoding( $name, "UTF-8" );
    $char_not_clean = array('/\?/','/\’/','/\'/','/À/','/Á/','/Â/','/Ã/','/Ä/','/Å/','/Ç/','/È/','/É/','/Ê/','/Ë/','/Ì/','/Í/','/Î/','/Ï/','/Ò/','/Ó/','/Ô/','/Õ/','/Ö/','/Ù/','/Ú/','/Û/','/Ü/','/Ý/','/à/','/á/','/â/','/ã/','/ä/','/å/','/ç/','/è/','/é/','/ê/','/ë/','/ì/','/í/','/î/','/ï/','/ð/','/ò/','/ó/','/ô/','/õ/','/ö/','/ù/','/ú/','/û/','/ü/','/ý/','/ÿ/', '/©/');
    $clean 			= array('','-','-','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y','copy');
    $friendly_name	= preg_replace($char_not_clean, $clean, $name);
    $friendly_name  = sanitize_title($friendly_name);

    return $friendly_name . '.' . $extension;
}

/**
 * Default values for the settings
 */
function quickwebp_settings_default( $id ) {

    $settings_arr = array(
        'quickwebp_settings_conversion'                   => '1',
        'quickwebp_settings_conversion_quality'           => 'high',
        'quickwebp_settings_conversion_ignore_webp'       => array('checked'),
        'quickwebp_settings_conversion_save_original'     => array(),
        'quickwebp_settings_conversion_display_webp_mode' => 'disabled',
        'quickwebp_settings_resize'                       => '1',
        'quickwebp_settings_resize_value'                 => 2000,
        'quickwebp_settings_completion'                   => '1',
        'quickwebp_settings_completion_options'           => array(
            'title',
            'caption',
            'alt',
            'description'
        ),
        'quickwebp_settings_cleanup'                      => '1',
        'quickwebp_settings_paste_image'                  => '0',
        'quickwebp_settings_debug_mode'                   => '0',
    );

    return isset($settings_arr[$id]) ? $settings_arr[$id] : '';
}

/**
 * Get the QuickWebP debug log path.
 *
 * @return string
 */
function quickwebp_get_debug_log_path() {
    $upload_dir = wp_upload_dir();
    $base_dir   = trailingslashit( $upload_dir['basedir'] ) . 'quickwebp';

    if ( ! is_dir( $base_dir ) ) {
        wp_mkdir_p( $base_dir );
    }

    return trailingslashit( $base_dir ) . 'quickwebp-debug.log';
}

/**
 * Check whether QuickWebP debug mode is enabled.
 *
 * @return bool
 */
function quickwebp_is_debug_mode_enabled() {
    return '1' === (string) get_option( 'quickwebp_settings_debug_mode', quickwebp_settings_default( 'quickwebp_settings_debug_mode' ) );
}

/**
 * Write a debug message to the QuickWebP custom log file.
 *
 * @param string $message Debug message.
 * @param array  $context Optional contextual data.
 *
 * @return void
 */
function quickwebp_debug_log( $message, $context = array() ) {
    if ( ! quickwebp_is_debug_mode_enabled() ) {
        return;
    }

    $formatted_message = '[' . gmdate( 'Y-m-d H:i:s' ) . '] ' . sanitize_text_field( $message );

    if ( ! empty( $context ) ) {
        $formatted_message .= ' ' . wp_json_encode( $context );
    }

    error_log( $formatted_message . PHP_EOL, 3, quickwebp_get_debug_log_path() );
}

/**
 * Allowed tags for SVG files
 */
function quickwebp_allowed_tags_for_svg_files() {
    $allowedtags = array(
        'svg' => array(
            'class'               => true,
            'xmlns'               => true,
            'width'               => true,
            'height'              => true,
            'viewbox'             => true,
            'preserveaspectratio' => true,
            'fill'                => true,
            'aria-hidden'         => true,
            'focusable'           => true,
            'role'                => true,
        ),
        'path' => array(
            'fill'            => true,
            'fill-rule'       => true,
            'd'               => true,
            'transform'       => true,
            'stroke'          => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
        ),
        'polygon' => array(
            'fill'      => true,
            'fill-rule' => true,
            'points'    => true,
            'transform' => true,
            'focusable' => true,
        ),
        'rect' => array(
            'fill'      => true,
            'fill-rule' => true,
            'height'    => true,
            'width'     => true,
            'x'         => true,
            'y'         => true,
        ),
        'line' => array(
            'fill'         => true,
            'fill-rule'    => true,
            'x1'           => true,
            'x2'           => true,
            'y1'           => true,
            'y2'           => true,
            'stroke'       => true,
            'stroke-width' => true,
            'transform'    => true,
        ),
        'defs' => array(
            'id' => true,
        ),
        'clipPath' => array(
            'id' => true,
        ),
        'g' => array(
            'clip-path' => true,
            'mask'      => true,
        ),
        'circle' => array(
            'cx'   => true,
            'cy'   => true,
            'r'    => true,
            'fill' => true,
            'stroke' => true,
            'stroke-width' => true,
            'stroke-dasharray' => true,
            'stroke-linecap' => true,
        ),
        'mask' => array(
            'id'        => true,
            'fill'      => true,
            'style'     => true,
            'maskUnits' => true,
            'x'         => true,
            'y'         => true,
            'width'     => true,
            'height'    => true,
        ),
        'image' => array(
            'id'      => true,
            'href'    => true,
            'x'       => true,
            'y'       => true,
            'width'   => true,
            'height'  => true,
            'clip'    => true,
            'mask'    => true,
            'opacity' => true,
            'xlink:href' => true,
        ),
        'defs' => array(
            'id' => true,
        ),
        'pattern' => array(
            'id'      => true,
            'x'       => true,
            'y'       => true,
            'width'   => true,
            'height'  => true,
            'patternUnits' => true,
            'patternContentUnits' => true,
        ),
        'use' => array(
            'id' => true,
            'x'  => true,
            'y'  => true,
            'xlink:href' => true,
            'transform' => true,
        ),
    );
    return $allowedtags;     
}

/**
 * Build a QuickWebP Pro URL with tracking parameters.
 *
 * @param string $context         Stable location identifier for the CTA.
 * @param array  $additional_args Optional additional query args.
 *
 * @return string
 */
function quickwebp_get_pro_url( $context, $additional_args = array() ) {
    $base_url = 'https://solutions.leyoweb.com/products/quickwebp/';

    if ( class_exists( 'Quickwebp_Settings' ) && defined( 'Quickwebp_Settings::QUICKWEBP_GET_LICENSE_URL' ) ) {
        $base_url = Quickwebp_Settings::QUICKWEBP_GET_LICENSE_URL;
    }

    return add_query_arg(
        array_merge(
            $additional_args,
            array(
                'utm_source'   => 'quickwebp-plugin',
                'utm_medium'   => 'plugin',
                'utm_campaign' => 'upgrade-pro',
                'utm_content'  => sanitize_key( $context ),
            )
        ),
        $base_url
    );
}
