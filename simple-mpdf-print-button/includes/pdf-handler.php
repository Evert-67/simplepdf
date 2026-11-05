<?php
if (!defined('ABSPATH')) exit;
add_action('admin_post_smppb_generate_pdf', 'smppb_generate_pdf');
add_action('admin_post_nopriv_smppb_generate_pdf', 'smppb_generate_pdf');
function smppb_generate_pdf() {
    $post_id = isset($_GET['post_id']) && is_scalar($_GET['post_id']) ? absint($_GET['post_id']) : 0;
    $template_id = isset($_GET['template']) && is_scalar($_GET['template']) ? absint($_GET['template']) : 0;
    $preview = !empty($_GET['preview']);
    $template = get_post($template_id);
    if (!$template || $template->post_type !== 'pdf_template') wp_die('PDF template not found.', '', ['response' => 404]);
    if ($preview) {
        if (!current_user_can('manage_options') || !current_user_can('edit_post', $template_id)) wp_die('Access denied.', '', ['response' => 403]);
        check_admin_referer('smppb_preview_' . $template_id);
    } elseif ($template->post_status !== 'publish') {
        wp_die('PDF template is not available.', '', ['response' => 404]);
    }
    $post = $post_id > 0 ? get_post($post_id) : null;
    if ($preview && !$post_id) {
        $post = new WP_Post((object)[
            'ID' => 0, 'post_author' => get_current_user_id(), 'post_title' => 'PDF template preview',
            'post_content' => '<h2>Example heading</h2><p>This preview uses sample content. Accents: café, naïef. Euro: €.</p><table><tr><th>Item</th><th>Quantity</th></tr><tr><td>Example</td><td>2</td></tr></table>' . str_repeat('<p>Example paragraph for testing page layout and page breaks.</p>', 45),
            'post_excerpt' => 'Example excerpt', 'post_status' => 'publish', 'post_type' => 'post',
            'post_date' => current_time('mysql'), 'post_modified' => current_time('mysql'), 'post_password' => ''
        ]);
    } elseif (!smppb_can_read($post)) {
        wp_die('This content is unavailable or you do not have permission to export it. Sign in or enter the post password on the original page first.', '', ['response' => 403]);
    }
    $code = get_post_meta($template_id, '_smppb_template_code', true);
    if (!is_string($code) || !trim($code)) wp_die('This PDF template is empty.', '', ['response' => 422]);
    if (!extension_loaded('mbstring') || !extension_loaded('gd') || !is_file(SMPPB_PATH . 'mpdf/autoload.php')) {
        wp_die('PDF generation requires the bundled mPDF library and the PHP mbstring and gd extensions.', '', ['response' => 503]);
    }
    $original_post = $GLOBALS['post'] ?? null;
    $original_shortcodes = $GLOBALS['shortcode_tags'];
    $buffer_level = ob_get_level();
    try {
        require_once SMPPB_PATH . 'mpdf/autoload.php';
        $GLOBALS['post'] = $post;
        setup_postdata($post);
        $GLOBALS['smppb_is_pdf_rendering'] = true;
        foreach (array_filter(array_map('trim', explode(',', get_option('smppb_ignored_shortcodes', '')))) as $tag) add_shortcode($tag, '__return_empty_string');
        ob_start();
        $html = do_shortcode($code);
        ob_end_clean();
        $temp = trailingslashit(get_temp_dir()) . 'smppb-' . substr(hash('sha256', ABSPATH . wp_salt()), 0, 20);
        if (!is_dir($temp) && !wp_mkdir_p($temp)) throw new RuntimeException('Unable to create PDF temporary directory.');
        $mpdf = new \Mpdf\Mpdf(['tempDir' => $temp]);
        $mpdf->SetTitle(wp_strip_all_tags($post->post_title));
        $mpdf->WriteHTML($html);
        $pdf = $mpdf->Output('', 'S');
    } catch (\Throwable $error) {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('Simple mPDF Print: ' . $error->getMessage());
        $pdf = null;
    } finally {
        while (ob_get_level() > $buffer_level) ob_end_clean();
        $GLOBALS['shortcode_tags'] = $original_shortcodes;
        unset($GLOBALS['smppb_is_pdf_rendering']);
        $GLOBALS['post'] = $original_post;
        if ($original_post) setup_postdata($original_post);
    }
    if ($pdf === null) wp_die('The PDF could not be generated. Check the template and server configuration. Technical details are logged when WordPress debugging is enabled.', '', ['response' => 500]);
    if (headers_sent()) wp_die('The PDF could not be sent because another component already produced output.', '', ['response' => 500]);
    $filename = smppb_pdf_filename($post, $template);
    $mode = smppb_output_mode($_GET['output'] ?? 'inherit', $preview);
    nocache_headers();
    header('Content-Type: application/pdf');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: ' . smppb_content_disposition($filename, $mode));
    echo $pdf;
    exit;
}
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (!extension_loaded('mbstring') || !extension_loaded('gd') || !is_file(SMPPB_PATH . 'mpdf/autoload.php')) echo '<div class="notice notice-error"><p>Simple mPDF Print Button: PDF generation requires the bundled mPDF library and PHP mbstring and gd.</p></div>';
});
