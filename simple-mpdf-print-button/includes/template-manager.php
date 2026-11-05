<?php
if (!defined('ABSPATH')) exit;

function smppb_manager_permission() { return current_user_can('manage_options'); }
function smppb_manager_error($message) { return new WP_Error('smppb_template', $message); }
function smppb_manager_templates() {
    return get_posts(['post_type'=>'pdf_template', 'post_status'=>['publish','draft','pending','private','future'], 'posts_per_page'=>-1, 'orderby'=>'title', 'order'=>'ASC']);
}
function smppb_template_bundle($ids) {
    if (!smppb_manager_permission()) return smppb_manager_error('Access denied.');
    if (!is_array($ids) || !$ids || count($ids)>100) return smppb_manager_error('Select between 1 and 100 templates.');
    $bundle=['format'=>'simple-mpdf-print-button','schema'=>1,'templates'=>[],'categories'=>[]];
    $categories=[];
    foreach (array_unique(array_map('absint',$ids)) as $id) {
        $post=get_post($id);
        if (!$post || $post->post_type!=='pdf_template' || !current_user_can('edit_post',$id)) return smppb_manager_error('Template unavailable.');
        $terms=wp_get_object_terms($id,'pdf_template_category');
        if (is_wp_error($terms)) return $terms;
        $slugs=[];
        foreach ($terms as $term) {
            $slugs[]=$term->slug;
            $lineage=array_merge([$term->term_id],get_ancestors($term->term_id,'pdf_template_category','taxonomy'));
            foreach ($lineage as $term_id) {
                $t=get_term($term_id,'pdf_template_category');
                if (!$t || is_wp_error($t)) return smppb_manager_error('Category unavailable.');
                $parent=$t->parent ? get_term($t->parent,'pdf_template_category') : null;
                $categories[$t->slug]=['slug'=>$t->slug,'name'=>$t->name,'description'=>$t->description,'parent'=>$parent && !is_wp_error($parent) ? $parent->slug : ''];
            }
        }
        $bundle['templates'][]=['title'=>$post->post_title,'description'=>$post->post_excerpt,'code'=>(string)get_post_meta($id,'_smppb_template_code',true),'categories'=>$slugs];
    }
    $bundle['categories']=array_values($categories);
    $valid=smppb_validate_bundle($bundle);
    return is_wp_error($valid) ? $valid : $bundle;
}
function smppb_validate_bundle($bundle) {
    if (!is_array($bundle) || ($bundle['format']??'')!=='simple-mpdf-print-button' || ($bundle['schema']??null)!==1) return smppb_manager_error('Unsupported template file.');
    if (!isset($bundle['templates'],$bundle['categories']) || !is_array($bundle['templates']) || !is_array($bundle['categories']) || !$bundle['templates'] || count($bundle['templates'])>100 || count($bundle['categories'])>500) return smppb_manager_error('Invalid template or category count.');
    $cats=[];
    foreach ($bundle['categories'] as $cat) {
        if (!is_array($cat)) return smppb_manager_error('Invalid category.');
        foreach (['slug','name','description','parent'] as $key) if (!isset($cat[$key]) || !is_string($cat[$key])) return smppb_manager_error('Invalid category field.');
        if (!$cat['slug'] || sanitize_title($cat['slug'])!==$cat['slug'] || isset($cats[$cat['slug']]) || !trim(sanitize_text_field($cat['name'])) || strlen($cat['slug'])>200 || strlen($cat['name'])>1000) return smppb_manager_error('Invalid or duplicate category slug/name.');
        $cats[$cat['slug']]=$cat;
    }
    foreach ($cats as $slug=>$cat) {
        $seen=[$slug=>true];$parent=$cat['parent'];
        while ($parent!=='') {
            if (!isset($cats[$parent]) || isset($seen[$parent])) return smppb_manager_error('Missing category parent or circular hierarchy.');
            $seen[$parent]=true;$parent=$cats[$parent]['parent'];
        }
    }
    foreach ($bundle['templates'] as $template) {
        if (!is_array($template)) return smppb_manager_error('Invalid template.');
        foreach (['title','description','code'] as $key) if (!isset($template[$key]) || !is_string($template[$key])) return smppb_manager_error('Invalid template field.');
        if (!trim(sanitize_text_field($template['title'])) || strlen($template['title'])>1000 || strlen($template['code'])>1024*1024 || !isset($template['categories']) || !is_array($template['categories'])) return smppb_manager_error('Invalid title, code size or categories.');
        foreach ($template['categories'] as $slug) if (!is_string($slug) || !isset($cats[$slug])) return smppb_manager_error('Unknown template category.');
    }
    $encoded=wp_json_encode($bundle);
    if ($encoded===false || strlen($encoded)>5*1024*1024) return smppb_manager_error('Template file exceeds 5 MB or cannot be encoded.');
    return true;
}
function smppb_import_bundle($bundle) {
    if (!smppb_manager_permission()) return smppb_manager_error('Access denied.');
    $valid=smppb_validate_bundle($bundle);
    if (is_wp_error($valid)) return $valid;
    $created_posts=[];$created_terms=[];$term_ids=[];
    try {
        $pending=$bundle['categories'];
        while ($pending) {
            foreach ($pending as $index=>$cat) {
                if ($cat['parent']!=='' && !isset($term_ids[$cat['parent']])) continue;
                $existing=get_term_by('slug',$cat['slug'],'pdf_template_category');
                if ($existing) $id=$existing->term_id;
                else {
                    $term=wp_insert_term($cat['name'],'pdf_template_category',['slug'=>$cat['slug'],'description'=>$cat['description'],'parent'=>$cat['parent']!==''?$term_ids[$cat['parent']]:0]);
                    if (is_wp_error($term)) throw new RuntimeException($term->get_error_message());
                    $id=$term['term_id'];$created_terms[]=$id;
                }
                $term_ids[$cat['slug']]=(int)$id;unset($pending[$index]);
            }
        }
        foreach ($bundle['templates'] as $template) {
            $id=wp_insert_post(wp_slash(['post_type'=>'pdf_template','post_status'=>'draft','post_title'=>sanitize_text_field($template['title']),'post_excerpt'=>$template['description'],'post_author'=>get_current_user_id()]),true);
            if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
            $created_posts[]=$id;
            update_post_meta($id,'_smppb_template_code',wp_slash($template['code']));
            if (get_post_meta($id,'_smppb_template_code',true)!==$template['code']) throw new RuntimeException('Could not save template code.');
            $terms=wp_set_object_terms($id,array_map(function($slug)use($term_ids){return $term_ids[$slug];},$template['categories']),'pdf_template_category');
            if (is_wp_error($terms)) throw new RuntimeException($terms->get_error_message());
        }
        return $created_posts;
    } catch (Throwable $error) {
        foreach ($created_posts as $id) wp_delete_post($id,true);
        foreach (array_reverse($created_terms) as $id) wp_delete_term($id,'pdf_template_category');
        return smppb_manager_error('Import failed: '.$error->getMessage());
    }
}
function smppb_duplicate_template($id) {
    if (!smppb_manager_permission() || !current_user_can('edit_post',$id)) return smppb_manager_error('Access denied.');
    $bundle=smppb_template_bundle([$id]);
    if (is_wp_error($bundle)) return $bundle;
    $bundle['templates'][0]['title'].=' (copy)';
    $ids=smppb_import_bundle($bundle);
    return is_wp_error($ids) ? $ids : $ids[0];
}
function smppb_manager_actions() {
    echo '<h2>Template tools</h2><p>Duplicates and imports become new drafts. Existing templates are retained. JSON includes HTML/CSS, descriptions and category hierarchy; images stay linked to their original URLs.</p>';
    if (isset($_GET['smppb_imported']) && is_scalar($_GET['smppb_imported'])) echo '<div class="notice notice-success inline"><p>'.esc_html(absint($_GET['smppb_imported'])).' templates imported as drafts. Open “Manage all templates” to review and publish them.</p></div>';
    $templates=smppb_manager_templates();
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="smppb_export_templates">';
    wp_nonce_field('smppb_export_templates');
    echo '<p><label>Export templates (up to 100)<br><select name="template_ids[]" multiple size="5" style="min-width:260px;max-width:100%" required>';
    foreach ($templates as $t) echo '<option value="'.esc_attr($t->ID).'">'.esc_html($t->post_title.' — '.$t->post_status).'</option>';
    echo '</select></label></p><p>Use Ctrl/Cmd to select several templates.</p><p><button class="button">Export selected JSON</button></p></form>';
    echo '<form method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="smppb_import_templates">';
    wp_nonce_field('smppb_import_templates');
    echo '<p><label>Import template JSON (maximum 5 MB)<br><input type="file" name="template_file" accept=".json,application/json" required></label></p><p><button class="button">Import as drafts</button></p></form>';
}
add_filter('post_row_actions',function($actions,$post){
    if ($post->post_type==='pdf_template' && smppb_manager_permission() && current_user_can('edit_post',$post->ID)) {
        $url=wp_nonce_url(add_query_arg(['action'=>'smppb_duplicate_template','template_id'=>$post->ID],admin_url('admin-post.php')),'smppb_duplicate_'.$post->ID);
        $actions['smppb_duplicate']='<a href="'.esc_url($url).'">Duplicate template</a>';
    }
    return $actions;
},10,2);
add_action('admin_post_smppb_duplicate_template',function(){
    if (!smppb_manager_permission()) wp_die('Access denied.','',['response'=>403]);
    $id=isset($_GET['template_id']) && is_scalar($_GET['template_id']) ? absint($_GET['template_id']) : 0;
    check_admin_referer('smppb_duplicate_'.$id);
    $new=smppb_duplicate_template($id);
    if (is_wp_error($new)) wp_die(esc_html($new->get_error_message()),'',['response'=>400]);
    wp_safe_redirect(get_edit_post_link($new,'raw'));exit;
});
add_action('admin_post_smppb_export_templates',function(){
    if (!smppb_manager_permission()) wp_die('Access denied.','',['response'=>403]);
    check_admin_referer('smppb_export_templates');
    $ids=isset($_POST['template_ids']) && is_array($_POST['template_ids']) ? array_filter($_POST['template_ids'],'is_scalar') : [];
    $bundle=smppb_template_bundle($ids);
    if (is_wp_error($bundle)) wp_die(esc_html($bundle->get_error_message()),'',['response'=>400]);
    nocache_headers();header('Content-Type: application/json; charset=utf-8');header('X-Content-Type-Options: nosniff');header('Content-Disposition: attachment; filename="smppb-templates-'.gmdate('Y-m-d').'.json"');
    echo wp_json_encode($bundle,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;
});
add_action('admin_post_smppb_import_templates',function(){
    if (!smppb_manager_permission()) wp_die('Access denied.','',['response'=>403]);
    check_admin_referer('smppb_import_templates');
    $file=$_FILES['template_file']??null;
    if (!$file || !isset($file['error'],$file['tmp_name']) || $file['error']!==UPLOAD_ERR_OK || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || filesize($file['tmp_name'])>5*1024*1024) wp_die('Upload a valid JSON file of at most 5 MB.','',['response'=>400]);
    $bundle=json_decode(file_get_contents($file['tmp_name']),true,64);
    $ids=smppb_import_bundle($bundle);
    if (is_wp_error($ids)) wp_die(esc_html($ids->get_error_message()),'',['response'=>400]);
    wp_safe_redirect(add_query_arg(['page'=>'smppb_templates','smppb_imported'=>count($ids)],admin_url('admin.php')));exit;
});

add_action('add_meta_boxes',function(){add_meta_box('smppb-preview-source','PDF preview content','smppb_preview_source_box','pdf_template','side');});
function smppb_preview_source_box($post) {
    if (!smppb_manager_permission()) return;
    $id=absint(get_post_meta($post->ID,'_smppb_preview_source',true));
    echo '<p><label>Search content<br><input id="smppb-source-search" type="search" style="width:100%" autocomplete="off" placeholder="Search title or content"></label></p><p id="smppb-source-status" role="status"></p><select id="smppb-source-results" aria-label="Search results" style="width:100%" hidden></select>';
    echo '<p><label>Preview post ID (0 = sample content)<br><input id="smppb-preview-source" type="number" min="0" name="smppb_preview_source" value="'.esc_attr($id).'" style="width:100%"></label></p>';
    if ($id) echo '<p>Saved source: '.esc_html(get_the_title($id)).'</p>';
    echo '<p>Save &amp; Test saves this form and opens the PDF in a new tab. The editor remains open. Normal Update/Publish keeps its usual behaviour.</p>';
}
add_action('save_post_pdf_template',function($id){
    if (!smppb_manager_permission() || !current_user_can('edit_post',$id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($id)) return;
    if (!isset($_POST['smppb_template_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['smppb_template_nonce'])),'smppb_template')) return;
    if (isset($_POST['smppb_preview_source']) && is_scalar($_POST['smppb_preview_source'])) update_post_meta($id,'_smppb_preview_source',absint($_POST['smppb_preview_source']));
},20);
add_action('admin_enqueue_scripts',function(){
    $screen=get_current_screen();
    if (!$screen || $screen->post_type!=='pdf_template') return;
    wp_enqueue_script('smppb-manager',SMPPB_URL.'assets/template-manager.js',[], '4.4.0',true);
    wp_localize_script('smppb-manager','smppbManager',['ajaxUrl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('smppb_source_search')]);
});
add_action('wp_ajax_smppb_source_search',function(){
    if (!smppb_manager_permission()) wp_send_json_error(['message'=>'Access denied.'],403);
    check_ajax_referer('smppb_source_search','nonce');
    $search=isset($_GET['search']) && is_string($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
    if (strlen($search)<2) wp_send_json_success([]);
    $posts=get_posts(['post_type'=>array_keys(smppb_post_types()),'post_status'=>['publish','private','draft','pending'],'s'=>$search,'posts_per_page'=>30,'orderby'=>'title','order'=>'ASC']);
    $results=[];
    foreach ($posts as $post) if (smppb_can_read($post)) $results[]=['id'=>$post->ID,'label'=>$post->post_title.' — '.$post->post_type.' #'.$post->ID];
    wp_send_json_success($results);
});
