<?php
if (!defined('ABSPATH')) exit;

/**
 * Public documentation only. Never reads saved templates or site options.
 */
function smppb_info_page_shortcode() {
    if (!empty($GLOBALS['smppb_is_pdf_rendering'])) return '';
    return file_get_contents(SMPPB_PATH . 'includes/views/plugin-info.html');
}
add_shortcode('simple_pdf_info', 'smppb_info_page_shortcode');
