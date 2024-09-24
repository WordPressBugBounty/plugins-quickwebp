<?php
/**
 * Bulk optimization button settings page
 * @since      1.0.0
 */

$status     = get_option( 'quickwebp_bulk_optimize_status', '' );
$is_running = $status == 'running' ? true : false;
$is_finish  = $status == 'finish' ? true : false;
$total      = (int)get_option( 'quickwebp_bulk_optimize_total', 0 );
$current    = (int)get_option( 'quickwebp_bulk_optimize_current', 0 );
$percent    = $total ? round( abs( ( $current / $total ) ) * 100 ) . '%' : '0%';
$progress   = $current . '/' . $total;
?>

<div class="quickwebp-bulk">

    <div class="quickwebp-bulk-optimization-top">
        
        <button class="quickwebp-bulk-optimization-btn-start button button-secondary <?php echo $is_running ? '' : 'show'; ?>">
            <?php _e( 'Start', QUICKWEBP_TEXT_DOMAIN ); ?>
            <div class="spinner"></div>
        </button>
    
        <button class="quickwebp-bulk-optimization-btn-stop button <?php echo $is_running ? 'show' : ''; ?>">
            <?php _e( 'Stop', QUICKWEBP_TEXT_DOMAIN ); ?>
        </button>

    </div>

    <div class="quickwebp-bulk-optimization-bottom">

        <div class="quickwebp-bulk-optimization-progress <?php echo $is_running ? 'show' : ''; ?>">
            <div class="quickwebp-bulk-optimization-progress-inner" style="width:<?php echo esc_attr($percent); ?>;"></div>
            <span class="quickwebp-bulk-optimization-progress-progress"><?php echo esc_html($progress); ?></span>
        </div>

        <p class="quickwebp-bulk-optimization-message description"></p>

    </div>

</div>