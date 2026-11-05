<?php
if (!defined('ABSPATH')) exit;

function smppb_positions() {
    return ['before' => 'Before content', 'after' => 'After content', 'both' => 'Before and after', 'none' => 'Manual only / hidden', 'floating-left' => 'Floating bottom left', 'floating-right' => 'Floating bottom right'];
}
function smppb_valid_position($value) { return is_string($value) && isset(smppb_positions()[$value]) ? $value : 'after'; }
function smppb_valid_width($value) {
    return is_string($value) && preg_match('/^(auto|(?:\d+(?:\.\d+)?)(?:px|%|em|rem|vw))$/D', trim($value)) ? trim($value) : '';
}
function smppb_alignment($value) { return in_array($value, ['left', 'center', 'right'], true) ? $value : 'left'; }
function smppb_post_types() { return get_post_types(['public' => true], 'objects'); }
function smppb_templates() {
    return get_posts(['post_type' => 'pdf_template', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
}
function smppb_template_select($name, $value, $inherit = false) {
    echo '<select name="' . esc_attr($name) . '">';
    if ($inherit) echo '<option value="-1" ' . selected($value, -1, false) . '>Use global setting</option>';
    echo '<option value="0" ' . selected($value, 0, false) . '>Template selector</option>';
    foreach (smppb_templates() as $template) echo '<option value="' . esc_attr($template->ID) . '" ' . selected($value, $template->ID, false) . '>' . esc_html($template->post_title) . '</option>';
    echo '</select>';
}
function smppb_placement_settings_rows() {
    smppb_presentation_settings_rows();
    $defaults = get_option('smppb_type_templates', []);
    echo '<tr><th>Template per post type</th><td>';
    foreach (smppb_post_types() as $type) {
        echo '<p><label>' . esc_html($type->labels->singular_name) . '<br>';
        smppb_template_select('smppb_type_templates[' . $type->name . ']', $defaults[$type->name] ?? -1, true);
        echo '</label></p>';
    }
    echo '</td><td>Automatic buttons: page override → post type → global default. Manual buttons keep their own template.</td></tr>';
    $types = get_option('smppb_post_types', array_keys(smppb_post_types()));
    echo '<tr><th>Automatic placement on</th><td>';
    foreach (smppb_post_types() as $type) {
        echo '<label><input type="checkbox" name="smppb_post_types[]" value="' . esc_attr($type->name) . '" ' . checked(in_array($type->name, (array)$types, true), true, false) . '> ' . esc_html($type->labels->singular_name) . '</label><br>';
    }
    echo '</td><td>Manual shortcodes and blocks remain available on all readable posts.</td></tr><tr><th>Default template</th><td>';
    smppb_template_select('smppb_default_template', get_option('smppb_default_template', 0));
    echo '</td><td>Choose a fixed template for a one-click button, or show a selector.</td></tr><tr><th>Button label</th><td><input name="smppb_button_label" value="' . esc_attr(get_option('smppb_button_label', 'Create PDF')) . '"></td><td>For automatic buttons and blocks.</td></tr><tr><th>Alignment</th><td><select name="smppb_alignment">';
    foreach (['left', 'center', 'right'] as $align) echo '<option ' . selected(get_option('smppb_alignment', 'left'), $align, false) . '>' . esc_html($align) . '</option>';
    echo '</select></td><td>Alignment within the available content width.</td></tr><tr><th>Require login</th><td><label><input type="checkbox" name="smppb_require_login" value="1" ' . checked(get_option('smppb_require_login', 0), 1, false) . '> Logged-in visitors only</label></td><td>Also enforced by the PDF endpoint. Post permissions and passwords are always checked.</td></tr>';
}
function smppb_save_placement_settings() {
    $types = isset($_POST['smppb_post_types']) && is_array($_POST['smppb_post_types']) ? array_filter(wp_unslash($_POST['smppb_post_types']), 'is_string') : [];
    update_option('smppb_post_types', array_values(array_intersect($types, array_keys(smppb_post_types()))));
    update_option('smppb_default_template', absint($_POST['smppb_default_template'] ?? 0));
    update_option('smppb_button_label', sanitize_text_field(wp_unslash($_POST['smppb_button_label'] ?? 'Create PDF')));
    update_option('smppb_alignment', smppb_alignment($_POST['smppb_alignment'] ?? 'left'));
    update_option('smppb_require_login', empty($_POST['smppb_require_login']) ? 0 : 1);
    $defaults = [];
    $input = isset($_POST['smppb_type_templates']) && is_array($_POST['smppb_type_templates']) ? $_POST['smppb_type_templates'] : [];
    foreach (smppb_post_types() as $type) {
        $value = isset($input[$type->name]) && is_scalar($input[$type->name]) ? max(-1, (int)$input[$type->name]) : -1;
        $template = $value > 0 ? get_post($value) : null;
        $defaults[$type->name] = $value > 0 && (!$template || $template->post_type !== 'pdf_template' || $template->post_status !== 'publish') ? -1 : $value;
    }
    update_option('smppb_type_templates', $defaults);
}
function smppb_can_read($post) {
    if (!$post || $post->post_type === 'pdf_template' || post_password_required($post)) return false;
    if (get_option('smppb_require_login', 0) && !is_user_logged_in()) return false;
    $allowed = is_post_publicly_viewable($post) || (is_user_logged_in() && current_user_can('read_post', $post->ID));
    // Membership plugins can impose additional restrictions; a true filter cannot bypass base checks.
    return $allowed && (bool) apply_filters('smppb_allow_pdf', true, $post, get_current_user_id());
}
function smppb_pdf_url($post_id, $template_id, $preview = false, $output = 'inherit') {
    $args = ['action' => 'smppb_generate_pdf', 'post_id' => absint($post_id), 'template' => absint($template_id)];
    if (!$preview && in_array($output, ['inline', 'download'], true)) $args['output'] = $output;
    if ($preview) { $args['preview'] = 1; $args['_wpnonce'] = wp_create_nonce('smppb_preview_' . absint($template_id)); }
    return add_query_arg($args, admin_url('admin-post.php'));
}
function smppb_render_control($post_id, $template_id = 0, $label = '', $class = '', $width = '', $alignment = 'left', $icon = 'inherit', $output = 'inherit', $size = 'inherit') {
    if (!empty($GLOBALS['smppb_is_pdf_rendering']) || !smppb_can_read(get_post($post_id))) return '';
    $label = is_string($label) && trim($label) !== '' ? $label : get_option('smppb_button_label', 'Create PDF');
    if (!is_string($label) || trim($label) === '') $label = 'Create PDF';
    $output = smppb_output_mode($output);
    $label_html = smppb_icon_markup($icon) . '<span class="smppb-label">' . esc_html($label) . '</span>';
    $width = smppb_valid_width($width);
    $style = $width ? ' style="display:inline-block;width:' . esc_attr($width) . '"' : '';
    if ($template_id > 0) {
        $template = get_post($template_id);
        if (!$template || $template->post_type !== 'pdf_template' || $template->post_status !== 'publish') return '';
        $classes = 'smppb-link ' . $class;
        $framework_classes = ['bootstrap' => ' btn btn-primary', 'tailwind' => ' bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-5 py-2.5', 'bulma' => ' button is-primary', 'materialize' => ' btn', 'foundation' => ' button primary', 'uikit' => ' uk-button uk-button-primary'];
        $classes .= $framework_classes[get_option('smppb_css_framework', '')] ?? '';
        $html = '<a href="' . esc_url(smppb_pdf_url($post_id, $template_id, false, $output)) . '" class="' . esc_attr($classes) . '"' . $style . ' target="' . ($output === 'download' ? '_self' : '_blank') . '" rel="noopener noreferrer">' . $label_html . '</a>';
    } else {
        if (!smppb_templates()) return '';
        $old_post = $GLOBALS['post'] ?? null;
        $GLOBALS['post'] = get_post($post_id);
        try { $html = smppb_legacy_selector('', $label_html, $output, $class); } finally { $GLOBALS['post'] = $old_post; }
        if ($width) $html = preg_replace('/style="width: [^"]*;"/', 'style="width: ' . esc_attr($width) . ';"', $html, 1);
    }
    return '<div ' . smppb_wrapper_attributes($alignment, $size) . '>' . $html . '</div>';
}
function smppb_print_pdf_button_shortcode($atts) {
    $atts = shortcode_atts(['template' => 0, 'label' => 'Print PDF', 'class' => 'print-pdf-button', 'width' => '', 'post_id' => 0, 'align' => 'left', 'icon' => 'inherit', 'output' => 'inherit', 'size' => 'inherit'], $atts, 'print_pdf_button');
    return smppb_render_control(absint($atts['post_id']) ?: get_the_ID(), absint($atts['template']), $atts['label'], $atts['class'], $atts['width'], $atts['align'], $atts['icon'], $atts['output'], $atts['size']);
}
add_shortcode('print_pdf_button', 'smppb_print_pdf_button_shortcode');
function smppb_effective_placement($post_id) {
    $position = get_post_meta($post_id, '_smppb_position', true);
    if (!$position || $position === 'inherit') $position = get_option('smppb_button_position', 'after');
    $template = get_post_meta($post_id, '_smppb_template', true);
    if ($template === '' || (int)$template < 0) {
        $defaults = get_option('smppb_type_templates', []);
        $type = get_post_type($post_id);
        $template = isset($defaults[$type]) && (int)$defaults[$type] >= 0 ? $defaults[$type] : get_option('smppb_default_template', 0);
    }
    return [smppb_valid_position($position), absint($template)];
}
function smppb_automatic_allowed() {
    if (is_admin() || is_feed() || !is_singular() || !empty($GLOBALS['smppb_is_pdf_rendering'])) return false;
    if (is_front_page() && get_option('smppb_disable_on_home', false)) return false;
    $post = get_post(get_queried_object_id());
    return $post && in_array($post->post_type, (array)get_option('smppb_post_types', array_keys(smppb_post_types())), true) && smppb_can_read($post);
}
function smppb_add_print_button($content) {
    if (!smppb_automatic_allowed() || !in_the_loop() || !is_main_query() || get_the_ID() !== get_queried_object_id()) return $content;
    list($position, $template) = smppb_effective_placement(get_the_ID());
    if (!in_array($position, ['before', 'after', 'both'], true)) return $content;
    $render = function () use ($template) { return smppb_render_control(get_the_ID(), $template, '', get_option('smppb_button_custom_class', ''), get_option('smppb_width_custom', ''), get_option('smppb_alignment', 'left')); };
    // Render independently to give each selector unique IDs.
    return ($position !== 'after' ? $render() : '') . $content . ($position !== 'before' ? $render() : '');
}
add_filter('the_content', 'smppb_add_print_button', 20);
add_action('wp_footer', function () {
    if (!smppb_automatic_allowed()) return;
    list($position, $template) = smppb_effective_placement(get_queried_object_id());
    if (strpos($position, 'floating-') !== 0) return;
    echo '<aside aria-label="PDF" class="smppb-floating smppb-' . esc_attr($position) . '">' . smppb_render_control(get_queried_object_id(), $template, '', get_option('smppb_button_custom_class', '')) . '</aside>';
});
add_action('wp_enqueue_scripts', function () {
    if (is_singular()) wp_enqueue_style('smppb-front', SMPPB_URL . 'assets/front.css', [], '4.4.1');
});
add_action('add_meta_boxes', function () {
    foreach (smppb_post_types() as $type) add_meta_box('smppb-placement', 'PDF button placement', 'smppb_placement_metabox', $type->name, 'side');
});
function smppb_placement_metabox($post) {
    wp_nonce_field('smppb_placement', 'smppb_placement_nonce');
    $value = get_post_meta($post->ID, '_smppb_position', true) ?: 'inherit';
    echo '<p><label>Position<br><select name="smppb_page_position">';
    foreach (['inherit' => 'Use global setting'] + smppb_positions() as $key => $label) echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
    echo '</select></label></p><p><label>Template<br>';
    $template = get_post_meta($post->ID, '_smppb_template', true);
    smppb_template_select('smppb_page_template', $template === '' ? -1 : (int)$template, true);
    echo '</label></p><p>Automatic placement must be enabled for this post type in the plugin settings.</p>';
}
add_action('save_post', function ($post_id) {
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['smppb_placement_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['smppb_placement_nonce'])), 'smppb_placement')) return;
    $position = wp_unslash($_POST['smppb_page_position'] ?? 'inherit');
    update_post_meta($post_id, '_smppb_position', $position === 'inherit' ? 'inherit' : smppb_valid_position($position));
    update_post_meta($post_id, '_smppb_template', max(-1, (int)($_POST['smppb_page_template'] ?? -1)));
});
add_action('init', function () {
    wp_register_script('smppb-block', SMPPB_URL . 'assets/block.js', ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'], '4.4.1', true);
    register_block_type('smppb/pdf-button', [
        'api_version' => 3, 'editor_script' => 'smppb-block', 'uses_context' => ['postId'],
        'attributes' => ['template' => ['type' => 'number', 'default' => 0], 'label' => ['type' => 'string', 'default' => 'Create PDF'], 'alignment' => ['type' => 'string', 'default' => 'left'], 'width' => ['type' => 'string', 'default' => ''], 'className' => ['type' => 'string', 'default' => ''], 'icon' => ['type' => 'string', 'default' => 'inherit'], 'output' => ['type' => 'string', 'default' => 'inherit'], 'size' => ['type' => 'string', 'default' => 'inherit']],
        'render_callback' => function ($attributes, $content, $block) {
            return smppb_render_control($block->context['postId'] ?? get_the_ID(), $attributes['template'], $attributes['label'], $attributes['className'], $attributes['width'], $attributes['alignment'], $attributes['icon'], $attributes['output'], $attributes['size']);
        }
    ]);
});
add_action('enqueue_block_editor_assets', function () {
    $templates = [['value' => 0, 'label' => 'Template selector']];
    foreach (smppb_templates() as $template) $templates[] = ['value' => $template->ID, 'label' => $template->post_title];
    wp_add_inline_script('smppb-block', 'window.smppbTemplates = ' . wp_json_encode($templates) . ';', 'before');
});

function smppb_templates_page() {
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><h1>PDF templates <a class="page-title-action" href="' . esc_url(admin_url('post-new.php?post_type=pdf_template')) . '">Add new</a></h1>';
    echo '<p><a href="' . esc_url(admin_url('edit.php?post_type=pdf_template')) . '">Manage all templates, drafts and trash</a></p>';
    smppb_manager_actions();
    echo '<table class="widefat striped"><thead><tr><th>Template</th><th>Shortcode</th></tr></thead><tbody>';
    foreach (smppb_templates() as $template) echo '<tr><td><a href="' . esc_url(get_edit_post_link($template->ID)) . '">' . esc_html($template->post_title) . '</a> · <a href="' . esc_url(wp_nonce_url(add_query_arg(['action'=>'smppb_duplicate_template','template_id'=>$template->ID],admin_url('admin-post.php')),'smppb_duplicate_'.$template->ID)) . '">Duplicate</a></td><td><code>[print_pdf_button template="' . esc_attr($template->ID) . '"]</code></td></tr>';
    echo '</tbody></table><p>Version 4.4.1: button appearance, icons, PDF download options and file naming. See Read Me for the complete guide.</p></div>';
}
