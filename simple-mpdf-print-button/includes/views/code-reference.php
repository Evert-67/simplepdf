<?php if (!defined('ABSPATH') || !current_user_can('manage_options')) return; ?>
<div class="wrap">
<h1>Simple PDF Print Button — Shortcode Reference · 4.4</h1>
<p>Use the button shortcode on your website. Use the content shortcodes inside PDF templates. See <a href="admin.php?page=smppb_manual">Read Me</a> for placement, access rules, examples and troubleshooting.</p>
<h2>Website buttons</h2>
<table class="widefat striped"><thead><tr><th>Example</th><th>Result</th></tr></thead><tbody>
<tr><td><code>[print_pdf_button template="123"]</code></td><td>A fixed-template PDF button. Replace 123 with your published template ID.</td></tr>
<tr><td><code>[print_pdf_button template="0" label="Create PDF"]</code></td><td>Show a template selector.</td></tr>
<tr><td><code>[print_pdf_button template="123" label="Download PDF" icon="download" output="download" size="large"]</code></td><td>Large download button with a bundled icon.</td></tr>
<tr><td><code>[print_pdf_button template="123" icon="print" output="inline" align="right" width="250px"]</code></td><td>Right-aligned button opening the PDF in a new tab.</td></tr>
<tr><td><code>[print_pdf_button template="123" post_id="456" class="my-button"]</code></td><td>Export another readable post and add your own CSS class.</td></tr>
</tbody></table>
<p><strong>Icon:</strong> inherit, none, document, download, print. <strong>Output:</strong> inherit, inline, download. <strong>Size:</strong> inherit, theme, small, normal, large. <strong>Align:</strong> left, center, right. <strong>Width:</strong> auto or a value in px, %, em, rem or vw. Colours are configured globally in Settings. Omitted icon/output/size attributes inherit their global defaults.</p>
<h2>Content shortcodes inside PDF templates</h2>
<table class="widefat striped"><thead><tr><th>Shortcode</th><th>Value</th></tr></thead><tbody>
<?php
$codes=[
'[post_content]'=>'Formatted source content; PDF controls are suppressed.',
'[post_title]'=>'Source title.',
'[post_author]'=>'Author display name.',
'[post_author_id]'=>'Author user ID.',
'[post_date]'=>'Publication date using the site date format.',
'[post_date_time]'=>'Publication date and time.',
'[post_modified]'=>'Last-modified date.',
'[post_permalink]'=>'Source permalink.',
'[post_excerpt]'=>'Source excerpt.',
'[post_categories]'=>'Category links.',
'[post_tags]'=>'Tag links.',
'[post_custom_field key="your_meta_key"]'=>'Value of the specified source custom field.',
'[post_id]'=>'Source post ID.',
'[post_status]'=>'Source post status.',
'[post_type]'=>'Source post type.',
'[page_number]'=>'mPDF page numbering, e.g. 1 of 3.',
'[pagebreak]'=>'Start a new PDF page.',
'[copyright_year]'=>'Current year with copyright symbol.',
'[copyright_year 2022]'=>'The specified year with copyright symbol.',
'[copyright_year 2022 2025]'=>'Explicit year range 2022–2025 (not automatically the current year).',
'[site_name]'=>'Site name.',
'[site_url]'=>'Site URL.',
'[site_logo width="100" height="100"]'=>'Configured site logo with dimensions; the legacy placeholder is used if no logo is set.'
];
foreach($codes as $code=>$description) echo '<tr><td><code>'.esc_html($code).'</code></td><td>'.esc_html($description).'</td></tr>';
?>
</tbody></table>
<p>These tags are WordPress shortcodes, not filename tokens. Filename patterns use braces: <code>{title}</code>, <code>{id}</code>, <code>{date}</code>, <code>{site}</code>, <code>{template}</code>. Configure them under <a href="admin.php?page=smppb_settings">Settings</a>.</p>
</div>
