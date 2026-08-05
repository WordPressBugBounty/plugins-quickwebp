<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Bulk optimization button settings page
 * @since      1.0.0
 */

$quickwebp_status     = get_option( 'quickwebp_bulk_optimize_status', '' );
$quickwebp_is_running = $quickwebp_status == 'running' ? true : false;
$quickwebp_is_finish  = $quickwebp_status == 'finish' ? true : false;
$quickwebp_total      = (int)get_option( 'quickwebp_bulk_optimize_total', 0 );
$quickwebp_current    = (int)get_option( 'quickwebp_bulk_optimize_current', 0 );
$quickwebp_percent    = $quickwebp_total ? round( abs( ( $quickwebp_current / $quickwebp_total ) ) * 100 ) . '%' : '0%';
$quickwebp_progress   = $quickwebp_current . '/' . $quickwebp_total;
?>

<div class="quickwebp-bulk">

    <div class="quickwebp-bulk-optimization-top">
        
        <button class="quickwebp-bulk-optimization-btn-start button button-secondary <?php echo esc_attr( $quickwebp_is_running ? '' : 'show' ); ?>">
            <?php esc_html_e( 'Start', 'quickwebp' ); ?>
            <div class="spinner"></div>
        </button>
    
        <button class="quickwebp-bulk-optimization-btn-stop button <?php echo esc_attr( $quickwebp_is_running ? 'show' : '' ); ?>">
            <?php esc_html_e( 'Stop', 'quickwebp' ); ?>
        </button>

    </div>

    <div class="quickwebp-bulk-optimization-bottom">

        <div class="quickwebp-bulk-optimization-progress <?php echo esc_attr( $quickwebp_is_running ? 'show' : '' ); ?>">
            <div class="quickwebp-bulk-optimization-progress-inner" style="width:<?php echo esc_attr($quickwebp_percent); ?>;"></div>
            <span class="quickwebp-bulk-optimization-progress-progress"><?php echo esc_html($quickwebp_progress); ?></span>
        </div>

        <p class="quickwebp-bulk-optimization-message description"></p>

    </div>

</div>