<?php
if (!defined('ABSPATH')) exit;

function smppb_appearance_defaults() {
    return ['enabled'=>0,'background'=>'#1e5fa8','text'=>'#ffffff','hover_background'=>'#174b85','hover_text'=>'#ffffff','border'=>'#1e5fa8','radius'=>6,'size'=>'theme','icon'=>'none'];
}
function smppb_sanitize_appearance($input) {
    $d=smppb_appearance_defaults();$input=is_array($input)?$input:[];$result=$d;
    $result['enabled']=empty($input['enabled'])?0:1;
    foreach (['background','text','hover_background','hover_text','border'] as $key) $result[$key]=isset($input[$key]) && is_string($input[$key]) ? (sanitize_hex_color($input[$key]) ?: $d[$key]) : $d[$key];
    foreach (['background','text','hover_background','hover_text','border'] as $key) if (strlen($result[$key])===4) $result[$key]='#'.$result[$key][1].$result[$key][1].$result[$key][2].$result[$key][2].$result[$key][3].$result[$key][3];
    $result['radius']=isset($input['radius']) && is_scalar($input['radius']) ? max(0,min(40,(int)$input['radius'])) : $d['radius'];
    $result['size']=isset($input['size']) && in_array($input['size'],['theme','small','normal','large'],true) ? $input['size'] : 'theme';
    $result['icon']=isset($input['icon']) && in_array($input['icon'],['none','document','download','print'],true) ? $input['icon'] : 'none';
    return $result;
}
function smppb_appearance() { return smppb_sanitize_appearance(get_option('smppb_appearance',smppb_appearance_defaults())); }
function smppb_output_mode($requested='inherit', $preview=false) {
    if ($preview) return 'inline';
    if (in_array($requested,['inline','download'],true)) return $requested;
    return get_option('smppb_pdf_output','inline')==='download' ? 'download' : 'inline';
}
function smppb_icon_markup($requested='inherit') {
    $icon=in_array($requested,['none','document','download','print'],true) ? $requested : smppb_appearance()['icon'];
    $paths=[
        'document'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>',
        'download'=>'<path d="M12 3v12m-5-5 5 5 5-5M5 16v4h14v-4"/>',
        'print'=>'<path d="M6 8V3h12v5M6 17H3V8h18v9h-3M6 14h12v7H6z"/><path d="M17 11h1"/>'
    ];
    return isset($paths[$icon]) ? '<svg class="smppb-icon" aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'.$paths[$icon].'</svg>' : '';
}
function smppb_wrapper_attributes($alignment='left',$size='inherit') {
    $a=smppb_appearance();
    $size=in_array($size,['theme','small','normal','large'],true)?$size:$a['size'];
    $classes='smppb-wrap smppb-align-'.smppb_alignment($alignment).' smppb-size-'.$size;
    if ($a['enabled']) $classes.=' smppb-custom';
    $style='';
    foreach (['background','text','hover_background','hover_text','border'] as $key) $style.='--smppb-'.str_replace('_','-',$key).':'.$a[$key].';';
    $style.='--smppb-radius:'.$a['radius'].'px;';
    return 'class="'.esc_attr($classes).'" style="'.esc_attr($style).'"';
}
function smppb_filename_pattern($value) {
    if (!is_string($value)) return '{title}';
    $value=trim(sanitize_text_field($value));
    if (!$value || mb_strlen($value)>180) return '{title}';
    $literal=str_replace(['{title}','{id}','{date}','{site}','{template}'],'',$value);
    if (strpbrk($literal,'{}')!==false) return '{title}';
    return $value;
}
function smppb_pdf_filename($post,$template) {
    $pattern=smppb_filename_pattern(get_option('smppb_filename_pattern','{title}'));
    $value=strtr($pattern,[
        '{title}'=>sanitize_title_with_dashes($post->post_title),
        '{id}'=>(string)absint($post->ID),
        '{date}'=>wp_date('Y-m-d'),
        '{site}'=>sanitize_title_with_dashes(get_bloginfo('name')),
        '{template}'=>sanitize_title_with_dashes($template->post_title)
    ]);
    $value=sanitize_file_name($value);
    $value=preg_replace('/(?:\.pdf)+$/i','',$value);
    $value=trim(mb_substr($value,0,180)," .\t\n\r\0\x0B");
    return ($value!=='' ? $value : 'document').'.pdf';
}
function smppb_content_disposition($filename,$mode) {
    // Bound to a single safe filename, including when called by another integration.
    $filename=sanitize_file_name(str_replace(["\r","\n"],'',$filename));
    $ascii=preg_replace('/[^a-zA-Z0-9._-]/','_',remove_accents($filename));
    if (!$ascii) $ascii='document.pdf';
    return ($mode==='download'?'attachment':'inline').'; filename="'.$ascii.'"; filename*=UTF-8\'\''.rawurlencode($filename);
}
function smppb_save_presentation_settings() {
    if (!current_user_can('manage_options')) return;
    update_option('smppb_appearance',smppb_sanitize_appearance(wp_unslash($_POST['smppb_appearance']??[])));
    update_option('smppb_pdf_output',($_POST['smppb_pdf_output']??'inline')==='download'?'download':'inline');
    update_option('smppb_filename_pattern',smppb_filename_pattern(wp_unslash($_POST['smppb_filename_pattern']??'{title}')));
}
function smppb_presentation_settings_rows() {
    $a=smppb_appearance();
    echo '<tr><th colspan="3"><h2>Button appearance &amp; PDF output — 4.4</h2></th></tr>';
    echo '<tr><th>Custom button colours</th><td><label><input type="checkbox" name="smppb_appearance[enabled]" id="smppb-custom-enabled" value="1" '.checked($a['enabled'],1,false).'> Enable plugin colours and rounded corners</label></td><td>Off by default: keep the theme/framework appearance. Applies to fixed buttons, selectors and floating controls.</td></tr>';
    $labels=['background'=>'Background','text'=>'Text and icon','hover_background'=>'Hover background','hover_text'=>'Hover text and icon','border'=>'Border'];
    foreach ($labels as $key=>$label) echo '<tr><th><label for="smppb-colour-'.$key.'">'.esc_html($label).'</label></th><td><input type="color" id="smppb-colour-'.$key.'" data-smppb-colour="'.esc_attr(str_replace('_','-',$key)).'" name="smppb_appearance['.$key.']" value="'.esc_attr($a[$key]).'"></td><td>Used when custom colours are enabled.</td></tr>';
    echo '<tr><th>Rounded corners</th><td><input id="smppb-radius" type="number" min="0" max="40" name="smppb_appearance[radius]" value="'.esc_attr($a['radius']).'"> px</td><td>0–40 pixels.</td></tr>';
    echo '<tr><th>Button size</th><td><select id="smppb-size" name="smppb_appearance[size]">';
    foreach (['theme'=>'Theme default','small'=>'Small','normal'=>'Normal','large'=>'Large'] as $key=>$label) echo '<option value="'.$key.'" '.selected($a['size'],$key,false).'>'.$label.'</option>';
    echo '</select></td><td>Size also works without custom colours. Can be overridden per shortcode/block.</td></tr>';
    echo '<tr><th>Button icon</th><td><select id="smppb-icon" name="smppb_appearance[icon]">';
    foreach (['none'=>'No icon','document'=>'Document','download'=>'Download','print'=>'Printer'] as $key=>$label) echo '<option value="'.$key.'" '.selected($a['icon'],$key,false).'>'.$label.'</option>';
    echo '</select></td><td>Built-in icons; no external icon library. The button label stays visible.</td></tr>';
    echo '<tr><th>Appearance preview</th><td><div id="smppb-appearance-preview" '.smppb_wrapper_attributes().'><button type="button" class="smppb-front-button">'.smppb_icon_markup().'<span class="smppb-label">Create PDF</span></button></div><p id="smppb-contrast" role="status"></p></td><td>Hover or focus to preview the colours. Your theme may add its own styling on the website.</td></tr>';
    echo '<tr><th>PDF behaviour</th><td><select name="smppb_pdf_output">';
    foreach (['inline'=>'Open PDF in a new tab','download'=>'Download PDF'] as $key=>$label) echo '<option value="'.$key.'" '.selected(smppb_output_mode(),$key,false).'>'.$label.'</option>';
    echo '</select></td><td>Save &amp; Test always opens a preview. Browser settings can affect PDF handling.</td></tr>';
    echo '<tr><th><label for="smppb-filename">PDF filename</label></th><td><input id="smppb-filename" type="text" name="smppb_filename_pattern" maxlength="180" class="regular-text" value="'.esc_attr(smppb_filename_pattern(get_option('smppb_filename_pattern','{title}'))).'"></td><td><code>{title}</code>, <code>{id}</code>, <code>{date}</code>, <code>{site}</code>, <code>{template}</code>. Example: <code>{site}-{title}-{date}</code>. The .pdf extension is added automatically; date means generation date in the site timezone.</td></tr>';
}
add_action('admin_enqueue_scripts',function($hook){
    if ($hook!=='simple-pdf-print_page_smppb_settings' && (!isset($_GET['page']) || $_GET['page']!=='smppb_settings')) return;
    wp_enqueue_style('smppb-front',SMPPB_URL.'assets/front.css',[],'4.4.0');
    wp_enqueue_script('smppb-appearance',SMPPB_URL.'assets/appearance.js',[],'4.4.0',true);
    $icons=[];foreach (['none','document','download','print'] as $icon) $icons[$icon]=smppb_icon_markup($icon);
    wp_localize_script('smppb-appearance','smppbAppearance',['icons'=>$icons]);
});
