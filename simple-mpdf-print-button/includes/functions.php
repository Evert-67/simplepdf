<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Voeg admin menu toe
 */
function smppb_admin_menu() {
    add_menu_page(
        'Simple mPDF Print',
        'Simple PDF Print',
        'manage_options',
        'smppb_templates',
        'smppb_templates_page',
        'dashicons-pdf',
        80
    );
	 add_submenu_page(
        'smppb_templates',                      
        'Categories',              
        'Categories',             
        'manage_options',                       
        'edit-tags.php?taxonomy=pdf_template_category&post_type=pdf_template'  
    );
	add_submenu_page(
        'smppb_templates',
        'Settings',
        'Settings',
        'manage_options',
        'smppb_settings',
        'smppb_settings_page'
    );
	add_submenu_page(
		'smppb_templates',              
		'Shortcode Reference',              
		'Shortcode Reference',               
		'manage_options',               
		'smppb_code_reference',         
		'smppb_code_reference_page'     
	);
    add_submenu_page(
        'smppb_templates',
        'Manual',
        'Read Me',
        'manage_options',
        'smppb_manual',
        'smppb_manual_page'
    );
}

add_action( 'admin_menu', 'smppb_admin_menu' );



add_action('admin_head', function() {
    echo '
    <style>
        #adminmenu .toplevel_page_smppb_templates div.wp-menu-image:before {
		color: #F4B400;
        }
    </style>';
});
function smppb_settings_page(){
    include SMPPB_PATH . 'includes/views/settings.php'; 
}
function smppb_code_reference_page() {
    include SMPPB_PATH . 'includes/views/code-reference.php';
}
function smppb_manual_page() {
    include SMPPB_PATH . 'includes/views/readme.php'; 
}





/**
 * Enqueue CodeMirror with PHP mode
 */
function smppb_enqueue_codemirror_assets($hook) {
    $screen = get_current_screen();

    // Alleen op PDF Template bewerkpagina
    if ($screen && $screen->post_type === 'pdf_template') {

        // Je eigen admin CSS & JS
        wp_enqueue_style('smppb-admin-css', SMPPB_URL . 'assets/admin.css');
        wp_enqueue_script('smppb-admin-js', SMPPB_URL . 'assets/admin.js', [], null, true);

        // CodeMirror Editor activeren voor PHP
        $settings = wp_enqueue_code_editor([
            'type' => 'text/html',
        ]);

        if ($settings) {
            wp_enqueue_script('wp-theme-plugin-editor');
            wp_enqueue_style('wp-codemirror');

            wp_add_inline_script('wp-theme-plugin-editor', sprintf(
                'jQuery(function($){
                    wp.codeEditor.initialize("template_content", %s);
                });',
                wp_json_encode($settings)
            ));
        }
    }
}

add_action('admin_enqueue_scripts', 'smppb_enqueue_codemirror_assets');

// 1. Kolom toevoegen
add_filter('manage_pdf_template_posts_columns', 'smppb_add_shortcode_column');
function smppb_add_shortcode_column($columns) {
    $columns['pdf_shortcode'] = __('Shortcode', 'smppb');
    return $columns;
}

// 2. Kolominhoud vullen
add_action('manage_pdf_template_posts_custom_column', 'smppb_fill_shortcode_column', 10, 2);
function smppb_fill_shortcode_column($column, $post_id) {
    if ($column === 'pdf_shortcode') {
        echo '<code>[print_pdf_button template="' . esc_attr($post_id) . '"]</code>';
    }
}



/**
 * Voeg de PDF print knop toe aan de inhoud
 */
function smppb_legacy_selector($content, $label_html = 'Create PDF', $output = 'inline', $extra_class = '') {
    if (!empty($GLOBALS['smppb_is_pdf_rendering'])) return $content;

    global $post;

    // Haal alle gepubliceerde PDF templates op uit het CPT
$template_options = '';
$grouped = get_option('smppb_group_by_category', false);

if ( $grouped ) {
    $terms = get_terms([
        'taxonomy' => 'pdf_template_category',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) $terms = [];
    foreach ( $terms as $term ) {
        $template_options .= '<optgroup label="' . esc_html($term->name) . '">';

        $templates_in_cat = get_posts([
            'post_type' => 'pdf_template',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => [[
                'taxonomy' => 'pdf_template_category',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ]]
        ]);

        foreach ( $templates_in_cat as $template ) {
            $desc = get_post_field( 'post_excerpt', $template->ID );
            if ( empty( $desc ) ) $desc = 'No description available';

            $template_options .= sprintf(
                '<option value="%d" data-desc="%s">%s</option>',
                esc_attr( $template->ID ),
                esc_attr( $desc ),
                esc_html( $template->post_title )
            );
        }

        $template_options .= '</optgroup>';
    }

    // Ongecategoriseerde
    $uncategorized_templates = get_posts([
        'post_type' => 'pdf_template',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'tax_query' => [[
            'taxonomy' => 'pdf_template_category',
            'operator' => 'NOT EXISTS',
        ]]
    ]);

    if ( $uncategorized_templates ) {
        $template_options .= '<optgroup label="No Category">';
        foreach ( $uncategorized_templates as $template ) {
            $desc = get_post_field( 'post_excerpt', $template->ID );
            if ( empty( $desc ) ) $desc = 'No description available';

            $template_options .= sprintf(
                '<option value="%d" data-desc="%s">%s</option>',
                esc_attr( $template->ID ),
                esc_attr( $desc ),
                esc_html( $template->post_title )
            );
        }
        $template_options .= '</optgroup>';
    }

} else {
    // platte lijst
    $templates = get_posts([
        'post_type' => 'pdf_template',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    foreach ( $templates as $template ) {
        $desc = get_post_field( 'post_excerpt', $template->ID );
        if ( empty( $desc ) ) $desc = 'Geen omschrijving beschikbaar';

        $template_options .= sprintf(
            '<option value="%d" data-desc="%s">%s</option>',
            esc_attr( $template->ID ),
            esc_attr( $desc ),
            esc_html( $template->post_title )
        );
    }
}




    $url_base = admin_url('admin-post.php');

		$custom_class   = get_option('smppb_button_custom_class', '');
		$select_custom  = get_option('smppb_select_custom_class', '');
		$desc_custom    = get_option('smppb_description_custom_class', '');
		$use_bootstrap  = get_option('smppb_use_bootstrap', false);
		$custom_width = smppb_valid_width(get_option('smppb_width_custom', '100%'));
        $uid = wp_unique_id('smppb-');
		$selected_framework = get_option('smppb_css_framework'); // 'bootstrap', 'tailwind', 'bulma', ...

		
		//extra classes via admin

		
		
		
		$button_classes = 'smppb-front-button ' . $extra_class;
		$select_classes = 'smppb-front-select';
		$desc_classes   = 'smppb-front-desc';

		switch ( $selected_framework ) {
			case 'bootstrap':
				$button_classes .= ' btn btn-primary';
				$select_classes .= ' form-select';
				$desc_classes   .= ' form-text';
				break;

			case 'tailwind':
				$button_classes .= ' bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-5 py-2.5';
				$select_classes .= ' bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5';
				$desc_classes   .= ' text-sm text-gray-500';
				break;

			case 'bulma':
				$button_classes .= ' button is-primary';
				$select_classes .= ' select';
				$desc_classes   .= ' help is-info';
				break;

			case 'materialize':
				$button_classes .= ' btn';
				$select_classes .= ' browser-default';
				$desc_classes   .= ' helper-text';
				break;

			case 'foundation':
				$button_classes .= ' button primary';
				$select_classes .= ' custom-select';
				$desc_classes   .= ' form-error';
				break;

			case 'uikit':
				$button_classes .= ' uk-button uk-button-primary';
				$select_classes .= ' uk-select';
				$desc_classes   .= ' uk-form-help';
				break;
		}

		// Voeg altijd extra admin-classes toe als ze zijn ingesteld
		if ( !empty($custom_class) )    $button_classes .= ' ' . esc_attr($custom_class);
		if ( !empty($select_custom) )   $select_classes .= ' ' . esc_attr($select_custom);
		if ( !empty($desc_custom) )     $desc_classes   .= ' ' . esc_attr($desc_custom);

		
		//wel of niet beschrijving via admin
		$desc_html = '';
			if ( get_option('smppb_show_template_description', false) ) {
				$desc_html = '<div data-smppb-description="1" class="' . esc_attr( $desc_classes ) . '"></div>';
			}
		
		$form_style = $custom_width ? 'style="width: ' . esc_attr( $custom_width ) . ';"' : '';

		$button = '
			<form class="smppb-control" action="' . esc_url( $url_base ) . '" method="get" target="' . ($output === 'download' ? '_self' : '_blank') . '"  ' . $form_style . '>
				<div class="input-group mb-3" style="margin-bottom: 1rem;">
					<input type="hidden" name="action" value="smppb_generate_pdf">
                    <input type="hidden" name="output" value="' . esc_attr($output) . '">
                    <input type="hidden" name="post_id" value="' . esc_attr( $post->ID ) . '">
					<select id="' . esc_attr($uid) . '" data-smppb-select="1" name="template" class="' . esc_attr( $select_classes ) . '" aria-label="PDF template" required>
						<option value="">Select template</option>' . $template_options . '
					</select>
					<button type="submit"  class="' . esc_attr( $button_classes ) . '">' . $label_html . '</button>
				</div>
				' . $desc_html . '
			</form>';


    return $button;
}


/**
 * Laad front-end scripts
 */
function smppb_enqueue_scripts() {
    if ( is_singular() ) {
        wp_enqueue_script( 'smppb-js', SMPPB_URL . 'assets/print-button.js', [], '4.4.0', true );
        wp_localize_script( 'smppb-js', 'smppbAjax', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'post_id'   => get_the_ID(),
        ]);
    }
}
add_action( 'wp_enqueue_scripts', 'smppb_enqueue_scripts' );


function smppb_handle_form_submissions() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $template_dir = SMPPB_PATH . 'templates/';

    // Instellingen opslaan
    if ( isset($_POST['smppb_settings_save']) && check_admin_referer('smppb_settings_action', 'smppb_settings_nonce') ) {
        update_option('smppb_disable_on_home', isset($_POST['smppb_disable_on_home']) ? 1 : 0);
        update_option('smppb_button_position', smppb_valid_position(wp_unslash($_POST['smppb_button_position'] ?? 'after')));
        update_option('smppb_show_template_description', isset($_POST['smppb_show_template_description']) ? 1 : 0);
        update_option('smppb_button_custom_class', sanitize_text_field($_POST['smppb_button_custom_class']));
		update_option('smppb_css_framework', sanitize_text_field($_POST['smppb_css_framework']));
        update_option('smppb_select_custom_class', sanitize_text_field($_POST['smppb_select_custom_class']));
        update_option('smppb_description_custom_class', sanitize_text_field($_POST['smppb_description_custom_class']));
        update_option('smppb_width_custom', smppb_valid_width(wp_unslash($_POST['smppb_width_custom'] ?? '')));
        smppb_save_placement_settings();
        smppb_save_presentation_settings(); 
		update_option('smppb_group_by_category', isset($_POST['smppb_group_by_category']) ? 1 : 0);
		update_option('smppb_ignored_shortcodes', sanitize_text_field($_POST['smppb_ignored_shortcodes']));
		update_option('smppb_ignored_shortcodes', sanitize_text_field($_POST['smppb_ignored_shortcodes']));




        // Redirect naar de juiste instellingenpagina mét bevestiging
        wp_redirect( add_query_arg([
            'page'     => 'smppb_settings', 
            'settings' => 'saved'
        ], admin_url('admin.php') ) );
        exit;
    }
}
add_action( 'admin_init', 'smppb_handle_form_submissions' );


add_action('add_meta_boxes', 'smppb_add_shortcode_metabox');

function smppb_add_shortcode_metabox() {
    add_meta_box(
        'smppb_shortcode_box',
        __('Print PDF Shortcode', 'smppb'),
        'smppb_render_shortcode_metabox',
        'pdf_template', // <== Zorg dat dit overeenkomt met je CPT slug
        'side',
        'high'
    );
}

function smppb_render_shortcode_metabox($post) {
    $shortcode = '[print_pdf_button template="' . esc_attr($post->ID) . '" Label="download PDF" Class="my_btn_class" width="100%"]';
    echo '<p>' . __('Use this shortcode to render a print button for this template:', 'smppb') . '</p>';
    echo '<code style="display:block;margin-bottom:8px;">' . $shortcode . '</code>';

    echo '<button class="button button-secondary smppb-copy-btn" type="button" data-clipboard-text="' . esc_attr($shortcode) . '">Copy Shortcode</button>';

    // Kleine JS voor copy
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.querySelector('.smppb-copy-btn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    const text = copyBtn.getAttribute('data-clipboard-text');
                    navigator.clipboard.writeText(text).then(() => {
                        copyBtn.textContent = 'Code Copied!';
                        setTimeout(() => copyBtn.textContent = 'Copy Shortcode', 2500);
                    });
                });
            }
        });
    </script>
    <?php
}
