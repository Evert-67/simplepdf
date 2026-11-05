<?php






function smppb_get_current_post_id() { return isset($GLOBALS['post']) ? (int)$GLOBALS['post']->ID : 0; }

function smppb_register_custom_post_shortcodes() {
    $shortcodes = [
        'post_content' => function() {
            return apply_filters('the_content', get_post_field('post_content', smppb_get_current_post_id()));
        },
        'post_title' => function() {
            return get_the_title(smppb_get_current_post_id());
        },
        'post_author' => function() {
            return get_the_author_meta('display_name', get_post_field('post_author', smppb_get_current_post_id()));
        },
        'post_author_id' => function() {
            return get_post_field('post_author', smppb_get_current_post_id());
        },
        'post_date' => function() {
            return get_the_date('', smppb_get_current_post_id());
        },
        'post_date_time' => function() {
            return get_the_date('j F Y H:i', smppb_get_current_post_id());
        },
        'post_modified' => function() {
            return get_the_modified_date('', smppb_get_current_post_id());
        },
        'post_permalink' => function() {
            return get_permalink(smppb_get_current_post_id());
        },
        'post_excerpt' => function() {
            return get_the_excerpt(smppb_get_current_post_id());
        },
        'post_categories' => function() {
            return get_the_category_list(', ', '', smppb_get_current_post_id());
        },
        'post_tags' => function() {
            return get_the_tag_list('', ', ', '', smppb_get_current_post_id());
        },
        'post_custom_field' => function($atts) {
            $atts = shortcode_atts(['key' => ''], $atts);
            return get_post_meta(smppb_get_current_post_id(), $atts['key'], true);
        },
        'post_id' => function() {
            return smppb_get_current_post_id();
        },
        'post_status' => function() {
            return get_post_status(smppb_get_current_post_id());
        },
        'post_type' => function() {
            return get_post_type(smppb_get_current_post_id());
        },
        'page_number' => function() {
            return '{PAGENO} of {nbpg}'; 
        },
    ];

    foreach ($shortcodes as $tag => $callback) {
        add_shortcode($tag, $callback);
    }
}
add_action('init', 'smppb_register_custom_post_shortcodes');


// **De 4 extra shortcodes apart registreren!**

add_shortcode('copyright_year', function ($atts = [], $content = null, $tag = '') {
    $jaar1 = '';
    $jaar2 = '';
    $huidigJaar = date('Y');

    // Haal ruwe attributen uit string
    if (is_array($atts)) {
        $jaar1 = isset($atts[0]) ? (int)$atts[0] : '';
        $jaar2 = isset($atts[1]) ? (int)$atts[1] : '';
    }

    // Als er geen beginjaar is, gebruik huidig jaar
    if (!$jaar1) {
        return '&copy; ' . $huidigJaar;
    }

    // Als er wel een beginjaar is maar geen eindjaar, gebruik huidig jaar
    if (!$jaar2 || $jaar1 == $jaar2) {
        return '&copy; ' . $jaar1;
    }

    // Beide jaren zijn aanwezig en verschillend
    return '&copy; ' . $jaar1 . '–' . $jaar2;
});

add_shortcode('pagebreak', function () {
    return '<pagebreak />';
});

add_shortcode('site_name', function () {
    return get_bloginfo('name');
});

add_shortcode('site_url', function () {
    return get_site_url();
});

add_shortcode('site_logo', function ($atts) {
    $atts = shortcode_atts([
        'width' => '',   // standaard geen breedte
        'height' => '150' // standaard hoogte 150
    ], $atts, 'site_logo');

    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

    $width_attr = $atts['width'] ? ' width="' . esc_attr($atts['width']) . '"' : '';
    $height_attr = $atts['height'] ? ' height="' . esc_attr($atts['height']) . '"' : '';

    if (has_custom_logo() && $logo_url) {
        return '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '"' . $width_attr . $height_attr . '>';
    } else {
        return '<img src="https://placehold.co/' . esc_attr($atts['width']) . 'x'. esc_attr($atts['height']) .'?text=logo" alt="Test Logo"' . $width_attr . $height_attr . '>';
    }
});

?>