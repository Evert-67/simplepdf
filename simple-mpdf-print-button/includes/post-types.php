<?php
/**
 * Register Custom Post Type: PDF Template and pdf-template-category
 */

function smppb_register_pdf_template_taxonomy() {
    register_taxonomy('pdf_template_category', 'pdf_template', [
        'labels' => [
            'name' => 'PDF Template Categories',
            'singular_name' => 'PDF Template Category',
            'search_items' => 'Search PDF Template Categories',
            'all_items' => 'All PDF Template Categories',
            'edit_item' => 'Edit PDF Template Category',
            'add_new_item' => 'Add New PDF Template Category',
            'new_item_name' => 'New PDF Template Category Name',
            'menu_name' => 'PDF Template Categories',
        ],
        'hierarchical' => true,  // net als gewone categorieën
        'show_ui' => true,
        'show_in_menu' => 'smppb_templates', // onder jouw custom admin menu
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'pdf-template-category'],
    ]);
}
add_action('init', 'smppb_register_pdf_template_taxonomy');


function smppb_register_pdf_template_cpt() {
    register_post_type('pdf_template', [
        'labels' => [
            'name' => 'All Templates',
            'singular_name' => 'PDF Template',
            'edit_item' => 'Edit Template',
            'add_new_item' => 'Add new Template',
            'new_item' => 'New Template',
            'view_item' => 'Review Template',
            'search_items' => 'Search Template',
            'not_found' => 'No template found',
            'not_found_in_trash' => 'No Templates in trashcan',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'smppb_templates',
        'supports' => ['title', 'excerpt', 'author', 'revisions'],
        'menu_icon' => 'dashicons-media-code',
        'taxonomies' => ['pdf_template_category'],  // aangepaste taxonomy hier
    ]);
}
add_action('init', 'smppb_register_pdf_template_cpt');



/**
 * Save template metadata (code and description)
 */
function smppb_save_codemirror_code($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id) || !current_user_can('manage_options')) return;
    if (!isset($_POST['smppb_template_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['smppb_template_nonce'])), 'smppb_template')) return;
    if (isset($_POST['smppb_template_code']) && is_string($_POST['smppb_template_code'])) {
        // Trusted administrator HTML/CSS; never execute PHP in templates.
        update_post_meta($post_id, '_smppb_template_code', wp_slash(wp_unslash($_POST['smppb_template_code'])));
    }
}
add_action('save_post_pdf_template', 'smppb_save_codemirror_code');

/**
 * Inject default template code when a new PDF Template is created
 */
 
function smppb_set_default_template_code($post_id, $post, $update) {
    if ($post->post_type !== 'pdf_template' || $update) return;

	$template_path = plugin_dir_path(__FILE__) . 'templates/default-template.txt';

    if (!file_exists($template_path)) return;
    $default_code = file_get_contents($template_path);

    update_post_meta($post_id, '_smppb_template_code', $default_code);
}
add_action('wp_insert_post', 'smppb_set_default_template_code', 10, 3);



/**
 * Add metabox with CodeMirror
 */
function smppb_add_codemirror_metabox() {
    add_meta_box(
        'smppb_template_code',
        'Template Editor',
        'smppb_codemirror_field_render',
        'pdf_template',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'smppb_add_codemirror_metabox');



/**
 * Render the metabox fields
 */
function smppb_codemirror_field_render_old($post) {
    $code = get_post_meta($post->ID, '_smppb_template_code', true);
    ?>
    <p><label><strong>Template Code (HTML, CSS & shortcodes):</strong><br>
        <textarea id="template_content" name="smppb_template_code" style="width:100%; height:400px;"><?php echo esc_textarea($code); ?></textarea>
</label></p>

    <button type="submit" class="smppb-save-btn">Save Template</button>
    <?php
}

function smppb_codemirror_field_render($post) {
    wp_nonce_field('smppb_template', 'smppb_template_nonce');
    if (!current_user_can('manage_options')) { echo '<p>Template code can only be edited by administrators.</p>'; return; }
    $code = get_post_meta($post->ID, '_smppb_template_code', true);
    ?>
    <p><label><strong>Template Code (HTML, CSS & shortcodes):</strong><br>
        <textarea id="template_content" name="smppb_template_code" style="width:100%; height:400px;"><?php echo esc_textarea($code); ?></textarea>
    </label></p>

    <div id="smppb-editor-buttons">
        <!-- Hier worden de standaard Publish-knoppen gekloond via JS -->
    </div>


    <?php
}




/**
 * Add save & Test Button.
 */
add_action('post_submitbox_misc_actions', function () {
    global $post;
    if (!$post || $post->post_type !== 'pdf_template' || !current_user_can('manage_options')) return;
    $test_url = smppb_pdf_url(absint(get_post_meta($post->ID, '_smppb_preview_source', true)), $post->ID, true);
    echo '<div class="misc-pub-section"><button type="submit" formtarget="_blank" name="smppb_save_test" value="1" class="button button-secondary">Save &amp; Test PDF</button>';
    echo ' <a id="smppb-saved-preview" target="_blank" rel="noopener" href="' . esc_url($test_url) . '">Preview saved template</a></div>';
});
add_filter('redirect_post_location', function ($location, $post_id) {
    if (!empty($_POST['smppb_save_test']) && get_post_type($post_id) === 'pdf_template' && current_user_can('manage_options') && current_user_can('edit_post', $post_id)
        && isset($_POST['smppb_template_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['smppb_template_nonce'])), 'smppb_template')) {
        return smppb_pdf_url(absint(get_post_meta($post_id, '_smppb_preview_source', true)), $post_id, true);
    }
    return $location;
}, 10, 2);
