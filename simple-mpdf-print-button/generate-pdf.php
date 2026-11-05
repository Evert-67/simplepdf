<?php
/** Compatibility redirect for existing bookmarks; WordPress loads via admin-post. */
$path = dirname(__DIR__, 3) . '/wp-load.php';
if (!is_file($path)) { http_response_code(410); exit('Please recreate the PDF link using the current plugin shortcode.'); }
require_once $path;
if (!function_exists('smppb_pdf_url')) { wp_die('PDF plugin is inactive.', '', ['response' => 503]); }
$post_id = isset($_GET['post_id']) && is_scalar($_GET['post_id']) ? absint($_GET['post_id']) : 0;
$template = isset($_GET['template']) && is_scalar($_GET['template']) ? absint($_GET['template']) : 0;
wp_safe_redirect(smppb_pdf_url($post_id, $template, $post_id === 0));
exit;
