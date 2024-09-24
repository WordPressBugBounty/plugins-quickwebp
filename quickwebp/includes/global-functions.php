<?php
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
        'quickwebp_settings_conversion'                     => '1',
        'quickwebp_settings_conversion_quality'             => 75,
        'quickwebp_settings_conversion_sharpen'             => 0,
        'quickwebp_settings_conversion_ignore_webp'         => array('checked'),
        'quickwebp_settings_conversion_save_original'       => array(),
        'quickwebp_settings_conversion_display_webp_mode'   => 'disabled',
        'quickwebp_settings_resize'                         => '1',
        'quickwebp_settings_resize_value'                   => 2000,
        'quickwebp_settings_completion'                     => '1',
        'quickwebp_settings_completion_options'             => array(
            'title',
            'caption',
            'alt',
            'description'
        ),
        'quickwebp_settings_cleanup'                        => '1',
        'quickwebp_settings_paste_image'                    => '0',
        'quickwebp_settings_library'                        => 'gd'
    );

    return isset($settings_arr[$id]) ? $settings_arr[$id] : '';
}