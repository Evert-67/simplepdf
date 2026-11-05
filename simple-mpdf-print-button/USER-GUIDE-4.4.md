# Simple mPDF Print Button 4.4.0 — User guide

A general-purpose WordPress plugin by Evert Beijes / CodePug.

## Installation and updates

1. Keep your previous plugin ZIP and back up the database, which contains templates and settings.
2. Upload the installable ZIP through **Plugins → Add New → Upload Plugin** and replace the existing plugin.
3. Open **Simple PDF Print → Settings** and review the options. Existing templates and options are retained.
4. Test an existing page with an existing template.

Requires WordPress 6.3+, PHP 8.0+, and the PHP extensions mbstring and gd. The dependency declaration supports PHP 8.0–8.4. The original integration tests used PHP 8.3; current GitHub checks also cover 8.0 and 8.4. This is not a compatibility guarantee for every future release.

## Button placement

Under **Settings → Button position**:

- **Before / After / Both:** above, below, or both.
- **Floating bottom left / right:** a floating button in the selected corner.
- **None:** manual shortcode or block placement only.

**Automatic placement on** selects post types. Public post types are selected when no setting has previously been saved. An empty selection disables automatic placement everywhere.

The editor's **PDF button placement** box sets the position and template for that post or page. **Use global setting** follows the defaults. Automatic placement must also be enabled for the post type.

Floating buttons can overlap chat or cookie controls. Choose a different corner or normal placement if needed. Your theme must call `wp_footer()` for floating buttons to appear.

## Manual placement

```text
[print_pdf_button template="123" label="Download PDF"]
[print_pdf_button template="123" label="Download PDF" align="right" width="250px" class="my-button"]
[print_pdf_button template="0" label="Create PDF" align="center"]
```

Replace `123` with a template ID. `template="0"` displays a template selector. An explicit `post_id` is supported, provided the visitor can read that content.

In Gutenberg, add the **PDF button** block and choose its template, label, alignment and width. The editor preview shows the button shape; the frontend uses the actual template selection and theme. Set the page's automatic position to **None** to avoid an extra button.

Supported widths are `auto`, `px`, `%`, `em`, `rem` and `vw`; CSS functions such as `calc()` are not accepted. Framework choices use the active theme's CSS; the plugin does not download frameworks. Existing global classes still apply to selectors. The shortcode's `class` applies to the button, including when using a selector.

## Templates and access

- Templates use HTML, mPDF-compatible CSS and WordPress shortcodes. PHP in a template is not executed.
- **Save & Test PDF** saves the classic template form and opens a protected preview in a new tab or window, depending on browser settings. The editor stays open. This uses the normal WordPress form submission with `formtarget="_blank"`; ordinary Update/Publish stays in the original tab. Because the editor does not reload, WordPress may still warn about unsaved changes when leaving. Reload after a successful save to refresh its saved state.
- **Preview saved template** opens the most recently saved template.
- Only administrators can save template code. Published templates are available to visitors who can read the source content.
- Private posts, drafts and passwords are checked on every PDF request. Enter post passwords on the original page first.
- **Require login** also applies to direct PDF links.
- Membership plugins can impose additional restrictions through `smppb_allow_pdf`. Universal membership-plugin compatibility is not assumed.

```php
add_filter('smppb_allow_pdf', function ($allowed, $post, $user_id) {
    return $allowed && my_membership_can_read($user_id, $post->ID);
}, 10, 3);
```

Replace `my_membership_can_read` with the membership plugin's actual API.

New buttons use WordPress's admin-post route. Old direct `generate-pdf.php` links redirect in standard WordPress directory layouts. Recreate manually written legacy links for relocated wp-content folders or custom installations. Shortcodes construct the correct new URLs automatically.

## Existing fixes

- Save & Test saves changes and uses a protected preview request.
- Multiple selectors have unique IDs and independent descriptions.
- Fixed shortcode button widths are applied through CSS.
- Buttons and the PDF button block are suppressed during PDF rendering.
- The main menu provides a working template overview.
- Template code preserves backslashes; the editor uses HTML mode.
- mPDF loads only for PDF generation. Missing extensions produce an administrator notice.
- PDF failures show readable messages; technical details enter the debug log only when WP_DEBUG is enabled.
- Existing templates, categories, shortcodes and settings remain in the database.

## New in 4.4: appearance and PDF output

Under **Button appearance & PDF output**, enable **Custom button colours** to set background, text, border and hover colours. Corner radius ranges from 0 to 40 pixels. Without this option, buttons retain the existing theme styling.

- **Size:** theme, small, normal or large. Size is independent of custom colours.
- **Icon:** none, document, download or print. Icons are bundled; no external library is needed.
- **Live preview:** inspect colours and hover/focus immediately. The contrast check compares the selected text and background colours; aim for at least 4.5:1. Also check your theme and mobile layout.
- **PDF behaviour:** open in a new tab or download. Download buttons target the current tab to avoid unnecessary blank tabs. Browser preferences can affect the result.
- **Save & Test** always uses an inline preview in a new tab, even when the global default is download.
- **PDF filename:** for example, `{site}-{title}-{date}`. Supported tokens: `{title}`, `{id}`, `{date}`, `{site}`, `{template}`. Date means generation date in the site's timezone. The `.pdf` extension is added automatically. Unknown tokens, empty patterns or patterns exceeding 180 characters fall back to `{title}`. Unsafe filename characters are removed.

```text
[print_pdf_button template="123" label="Download PDF" icon="download" output="download" size="large"]
[print_pdf_button template="123" label="Open PDF" icon="document" output="inline" size="normal"]
```

`icon`: inherit, none, document, download, print. `output`: inherit, inline, download. `size`: inherit, theme, small, normal, large. Omitted options inherit global settings. These options also appear in the Gutenberg block. Colours and filename patterns are global, not shortcode attributes.

These settings affect website buttons. PDF layout still comes from the HTML/CSS template. Download settings never bypass read permissions. The in-plugin Read Me and Shortcode Reference include updated navigation, release history and troubleshooting.

## New in 4.3: template management

- **Duplicate:** use Duplicate in the main menu for a published template, or Duplicate template in Manage all templates. The copy is a draft with the same HTML/CSS, description and categories, with “(copy)” added to its title.
- **Export:** select up to 100 templates and choose Export selected JSON. Ctrl/Cmd selects multiple items. Drafts can be included.
- **Import:** upload an exported JSON file up to 5 MB. Imports always create new drafts, never replacements. Review and publish them in Manage all templates.
- A template can contain up to 1 MB of code; an import can include up to 500 categories. The whole file is validated before import. Templates and categories newly created by a failed import are cleaned up.
- Existing categories with matching slugs are reused without changing their names or positions. New categories retain the exported hierarchy.
- Images, fonts and linked files are not embedded in JSON. Update their URLs or move the files separately if the source site is removed. Template exports are not complete website backups.
- Template IDs, page settings, preview post IDs and site settings are not transferred. Set the appropriate defaults after importing.

## Preview with real content

In **PDF preview content**, search for posts, pages or other public post types using at least two characters. Choose a result or enter a post ID directly. ID 0 uses sample text. Results are limited to 30 readable items.

Save & Test saves this selection and opens the PDF in a new tab. Preview saved template uses saved template code with the currently selected source. Access and password checks still apply. If the source is inaccessible, an error appears, but the template may already have been saved.

## Default templates per post type

Under **Settings → Template per post type**, choose separate templates for posts, pages or products. Only published templates can be selected.

Automatic buttons resolve templates in this order: explicit page setting → post-type setting → global default. “Template selector” is also an explicit choice. “Use global setting” on a page follows this chain, starting with the post type. Manual shortcodes and PDF button blocks retain their own template choice.

## Test on your site

After updating, check before/after/both placement, floating positions on mobile, your CSS framework, and a PDF containing a logo, tables and multiple pages. Check logged-out visitors and membership rules. Theme-specific presentation and existing custom templates require verification on your own site.

## Public information page (4.4.1)

Add `[simple_pdf_info]` in a Shortcode block on a page titled Simple PDF. Set automatic PDF button placement to None on that page. The static English guide contains copyable code examples and does not read saved templates. It is suppressed during PDF rendering. Its only promotional website link is CodePug. Set a theme logo before using `[site_logo]`; no external placeholder is fetched when a logo is absent.
