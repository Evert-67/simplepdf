<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) return;
?>
<div class="wrap smppb-manual">
<style>
.smppb-manual{max-width:1100px}.smppb-manual h2{margin-top:2em;padding-bottom:.4em;border-bottom:1px solid #c3c4c7}.smppb-manual table{width:100%;border-collapse:collapse;margin:1em 0;background:#fff}.smppb-manual td,.smppb-manual th{border:1px solid #c3c4c7;padding:10px;text-align:left;vertical-align:top}.smppb-manual pre{background:#fff;padding:16px;overflow:auto;border-left:4px solid #2271b1}.smppb-manual ul{list-style:disc;margin-left:24px}.smppb-manual .smppb-toc{display:flex;flex-wrap:wrap;gap:8px 20px;list-style:none;margin-left:0}.smppb-manual code{overflow-wrap:anywhere}
</style>
<h1>Simple mPDF Print Button — Read Me</h1>
<p><strong>Version 4.4.1 · Evert Beijes / Codepug</strong></p>
<p>Generate PDFs from WordPress content with your own HTML/CSS templates. Use automatic buttons, shortcodes or the PDF button block. Templates and settings are stored in the WordPress database.</p>
<ul class="smppb-toc">
<li><a href="#smppb-start">Quick start</a></li><li><a href="#smppb-placement">Placement</a></li><li><a href="#smppb-appearance">Appearance</a></li><li><a href="#smppb-output">PDF output</a></li><li><a href="#smppb-preview">Save &amp; Test</a></li><li><a href="#smppb-portability">Import/export</a></li><li><a href="#smppb-shortcodes">Shortcodes</a></li><li><a href="#smppb-access">Access</a></li><li><a href="#smppb-troubleshooting">Troubleshooting</a></li><li><a href="#smppb-changes">Changelog</a></li>
</ul>
<h2 id="smppb-start">Quick start</h2>
<ol>
<li>Install or replace the plugin ZIP through Plugins → Add New → Upload Plugin. Keep a database backup when updating.</li>
<li>Open <strong>Simple PDF Print</strong> in the main admin menu. Choose <strong>Add new</strong> to create a template.</li>
<li>Enter a title, optional excerpt/description and categories. Edit the supplied HTML/CSS and template shortcodes. PHP inside templates is not executed.</li>
<li>Use <strong>Save &amp; Test PDF</strong> to check the result. Publish the template when ready; visitors can only choose published templates.</li>
<li>Open <strong>Simple PDF Print → Settings</strong> to choose placement and defaults, or place a shortcode/block manually.</li>
</ol>
<p>Requirements: WordPress 6.3+, PHP 8.0+, PHP mbstring and gd. The bundled mPDF dependency declares support through PHP 8.4; these releases are integration-tested with WordPress 6.8.9 / PHP 8.3.33. No external CSS or icon library is downloaded.</p>
<h2 id="smppb-placement">Placement and default templates</h2>
<table><thead><tr><th>Setting</th><th>Behaviour</th></tr></thead><tbody>
<tr><td>Before / After / Both</td><td>Add a control above, below or on both sides of the main content.</td></tr>
<tr><td>Floating bottom left / right</td><td>Place a control in a screen corner on singular pages. The theme must call <code>wp_footer()</code>. Check for overlap with chat or cookie controls on mobile.</td></tr>
<tr><td>None</td><td>Disable automatic controls. Manual shortcodes and blocks still work.</td></tr>
<tr><td>Automatic placement on</td><td>Select the public post types. An empty selection disables automatic placement everywhere.</td></tr>
<tr><td>Hide on homepage</td><td>Disable automatic placement on the front page.</td></tr>
<tr><td>PDF button placement (page editor)</td><td>Override position and template for one page or post. Automatic placement must be enabled for its post type.</td></tr>
<tr><td>Template per post type</td><td>Automatic template priority: page override → post-type default → global default. “Template selector” is an explicit choice; “Use global setting” on a page follows this chain.</td></tr>
</tbody></table>
<p>Use the <strong>PDF button</strong> block inside content or columns. Its settings include template, label, alignment, width, icon, size and PDF behaviour. Manual blocks/shortcodes keep their own template choice. Choose None for automatic placement if you only want the manual control.</p>
<h2 id="smppb-appearance">Button appearance — new in 4.4</h2>
<p>In Settings, enable <strong>Custom button colours</strong> to control background, text/icon colour, hover colours, border and rounded corners (0–40px). This is off by default so existing websites retain their theme styling.</p>
<ul><li>Size: theme default, small, normal or large.</li><li>Icon: none, document, download or printer. Icons are bundled SVGs; the label remains visible.</li><li>The live settings preview shows colour changes and text contrast for normal and hover states. Aim for at least 4.5:1. This checks the selected colours, not the complete accessibility of a page.</li><li>Sizes and icons work independently of custom colours. Shortcodes and blocks can override size and icon; colours are global.</li><li>Bootstrap, Tailwind, Bulma, Materialize, Foundation and UIkit use the active theme's CSS. Select the matching framework only if the theme loads it. Custom CSS classes remain supported.</li></ul>
<p>The admin preview cannot reproduce every theme. Check the actual page, keyboard focus, dark mode and mobile layout after changing colours. These settings style website buttons; PDF page design still comes from template HTML/CSS.</p>
<h2 id="smppb-output">Open or download, and filename patterns</h2>
<p><strong>PDF behaviour</strong> chooses between opening a PDF in a new tab and requesting a download. A shortcode/block can override this. Save &amp; Test always opens an inline preview. Browsers may apply their own PDF preferences.</p>
<p>Set a global filename pattern, for example <code>{site}-{title}-{date}</code>. The plugin adds <code>.pdf</code> automatically and removes unsafe filename characters. The default remains <code>{title}</code>.</p>
<table><thead><tr><th>Token</th><th>Value</th></tr></thead><tbody>
<tr><td><code>{title}</code></td><td>Source post/page title as a filename-friendly slug.</td></tr><tr><td><code>{id}</code></td><td>Source post ID; 0 for sample preview content.</td></tr><tr><td><code>{date}</code></td><td>Generation date, YYYY-MM-DD, in the site's timezone.</td></tr><tr><td><code>{site}</code></td><td>Site name as a slug.</td></tr><tr><td><code>{template}</code></td><td>Template title as a slug.</td></tr>
</tbody></table>
<p>Patterns are limited to 180 characters. Unknown brace tokens or an empty pattern fall back to <code>{title}</code>. Filenames are not taken from visitor-supplied filename parameters. This setting does not rename posts or templates.</p>
<h2 id="smppb-preview">Save &amp; Test and real preview content</h2>
<ol><li>In the template editor, find <strong>PDF preview content</strong>.</li><li>Search using at least two characters and select a readable post/page, or enter its ID. Search returns up to 30 matches. ID 0 uses sample content.</li><li>Click <strong>Save &amp; Test PDF</strong>. WordPress first saves the form, including template code, title, excerpt, categories and preview source. The resulting PDF opens in a new tab/window while the editor stays open.</li></ol>
<p><strong>Preview saved template</strong> uses the last saved template code with the currently selected source; it does not save unsaved edits. Normal Update/Publish keeps its normal behaviour. Because Save &amp; Test leaves the editor open, WordPress may still show an unsaved-changes prompt until you reload the editor after a successful save.</p>
<p>Preview requires administrator access and a valid security token. The source must be readable and its post password must already have been entered. If a PDF fails, the template may already be saved; check the source and template before retrying.</p>
<h2 id="smppb-portability">Duplicate, import and export templates</h2>
<ul><li><strong>Duplicate:</strong> use Duplicate in the main template overview or Duplicate template in Manage all templates. A new draft includes code, description and categories.</li><li><strong>Export:</strong> select 1–100 templates and choose Export selected JSON. Use Ctrl/Cmd to select several. Drafts can be exported too.</li><li><strong>Import:</strong> upload a template JSON of at most 5 MB. Imports create new drafts; review and publish them in Manage all templates.</li><li>The JSON includes HTML/CSS, descriptions and category hierarchy. Images, fonts and other files remain linked by URL and are not embedded. Move those files separately when needed.</li><li>Existing category slugs are reused without renaming or moving them. New categories get the exported hierarchy. Existing templates are never overwritten.</li><li>The file is validated before import; limits are 1 MB of code per template and 500 categories. Newly created posts/categories are rolled back on a detected import failure.</li><li>Site settings, post IDs, preview source IDs and per-page assignments are not transferred. Choose defaults again on the destination site.</li></ul>
<h2 id="smppb-shortcodes">Shortcodes and template content</h2>
<p>Replace 123 with a published template ID:</p>
<pre><code>[print_pdf_button template="123" label="Download PDF" icon="download" output="download" size="large"]
[print_pdf_button template="0" label="Create PDF" align="right" width="100%"]
[print_pdf_button template="123" post_id="456" class="my-button"]</code></pre>
<table><thead><tr><th>Attribute</th><th>Allowed values / meaning</th></tr></thead><tbody>
<tr><td>template</td><td>Published template ID; 0 shows the template selector.</td></tr><tr><td>post_id</td><td>Optional source ID. Otherwise use the current post. Source access checks still apply.</td></tr><tr><td>label / class</td><td>Button text / custom CSS classes. An empty configured label falls back to Create PDF.</td></tr><tr><td>align / width</td><td>left, center, right / auto or a number followed by px, %, em, rem or vw. No calc() expressions.</td></tr><tr><td>icon</td><td>inherit (default), none, document, download, print.</td></tr><tr><td>size</td><td>inherit (default), theme, small, normal, large.</td></tr><tr><td>output</td><td>inherit (default), inline, download. Invalid values follow the global setting.</td></tr>
</tbody></table>
<p>Common template shortcodes:</p>
<pre><code>&lt;h1&gt;[post_title]&lt;/h1&gt;
[post_content]
&lt;p&gt;Updated: [post_modified]&lt;/p&gt;
&lt;p&gt;Reference: [post_custom_field key="reference"]&lt;/p&gt;
&lt;p&gt;Page [page_number]&lt;/p&gt;</code></pre>
<p>Other available tags include <code>[post_excerpt]</code>, <code>[post_author]</code>, <code>[post_date]</code>, <code>[site_name]</code>, <code>[site_url]</code>, <code>[site_logo]</code> and <code>[pagebreak]</code>. See <strong>Shortcode Reference</strong> for the full list. “Shortcodes to ignore” accepts comma-separated tag names without brackets. PDF buttons are automatically suppressed during PDF rendering.</p>
<p>mPDF supports its own subset of HTML/CSS. Browser layout systems such as flex/grid do not necessarily carry into PDFs; use template CSS suitable for mPDF. Keep website button CSS and PDF layout CSS separate.</p>
<h2 id="smppb-access">Access and compatibility</h2>
<p>Private posts, drafts and password-protected content are checked on every PDF request. Require login applies to direct PDF links too. Only administrators can save template code or use the import/export tools. Draft templates are for authorized preview, not public export.</p>
<p>Membership plugins can impose extra rules with <code>smppb_allow_pdf</code>. Example for developers, replacing the sample function with the membership plugin's actual API:</p>
<pre><code>add_filter('smppb_allow_pdf', function ($allowed, $post, $user_id) {
    return $allowed &amp;&amp; my_membership_can_read($user_id, $post-&gt;ID);
}, 10, 3);</code></pre>
<p>New links use WordPress admin-post. Old generate-pdf.php bookmarks redirect on a standard WordPress folder structure. Recreate manually stored old links after moving wp-content to a different location.</p>
<h2 id="smppb-troubleshooting">Troubleshooting</h2>
<table><thead><tr><th>Symptom</th><th>Check</th></tr></thead><tbody>
<tr><td>No automatic button</td><td>Published template, post-type selection, homepage setting, per-page position, visitor access and main content loop.</td></tr><tr><td>Button appears twice</td><td>Disable automatic placement if a manual shortcode/block is already present.</td></tr><tr><td>Colour changes not visible</td><td>Enable Custom button colours, save settings, clear page caches and check theme CSS overrides.</td></tr><tr><td>PDF opens instead of downloading</td><td>Global output setting, shortcode/block override and browser PDF preferences.</td></tr><tr><td>Preview denied or expired</td><td>Sign in as administrator, reload the template editor and check source access/password.</td></tr><tr><td>PDF generation fails</td><td>mbstring/gd extensions, non-empty template, temporary directory permissions and template HTML. Technical details go to the debug log when WP_DEBUG is enabled.</td></tr><tr><td>Missing PDF images</td><td>Check image URLs and server access to those files, especially after moving templates to another website.</td></tr>
</tbody></table>
<h2 id="smppb-changes">Changelog</h2>
<ul><li><strong>4.4:</strong> optional button colours, hover/border/radius, size, bundled icons, live colour/contrast preview, inline/download selection, filename patterns, updated Read Me and shortcode examples.</li><li><strong>4.3:</strong> duplication, template JSON import/export, real-content preview, per-post-type template defaults, Save &amp; Test in a new tab.</li><li><strong>4.2:</strong> access checks, repaired template saving, multiple independent controls, per-page placement, floating buttons, Gutenberg block and working template overview.</li></ul>
</div>

<section><h2>Public information page</h2><p>Add <code>[simple_pdf_info]</code> to a WordPress page to display the English plugin overview, setup guide and examples. Set automatic PDF button placement to None for that page. Saved templates are never listed on the information page.</p></section>
