# Simple mPDF Print Button 4.4.2

A WordPress plugin by Evert Beijes / CodePug for PDF buttons and custom HTML/CSS templates.

## Installation

**[Download the installable WordPress plugin ZIP](https://github.com/Evert-67/simplepdf/releases/latest/download/simple-mpdf-print-button-4.4.2.zip)**

Upload this ZIP directly through **WordPress → Plugins → Add New → Upload Plugin**. Do not extract it first. If updating, choose to replace the existing plugin.

Use the named plugin ZIP under [Releases](https://github.com/Evert-67/simplepdf/releases/latest). **Code → Download ZIP**, `main.zip`, and the automatic **Source code** downloads are development archives, not WordPress installation packages. The release ZIP includes mPDF and its fonts; the source archive does not.

The plugin code was imported from the original 4.4.0 distribution. Composer installs the dependency versions and source references recorded in that distribution. The plugin continues to use `mpdf/autoload.php`.

## Features

- Manage, duplicate, import and export templates.
- Save & Test: save changes and open a PDF preview in a new tab.
- Default templates per post type and previews with selected content.
- Button colours, icons, sizes and rounded corners.
- Open or download PDFs with configurable filenames.

See the [user guide](simple-mpdf-print-button/USER-GUIDE-4.4.md).

## Automated checks

GitHub Actions checks PHP and JavaScript syntax, WordPress 6.8.3 activation, colour validation, filenames, safe headers, preview/download selection, and actual mPDF generation with text extraction on PHP 8.0, 8.3 and 8.4. Tests use real WordPress functions. The PDF smoke test exercises mPDF after plugin activation; it does not cover the full HTTP button route or browser interaction.

The [original test report](simple-mpdf-print-button/TEST-REPORT-4.4.md) is historical documentation, not a claim about the current CI run. Current results are available under Actions. Tests send no emails and do not modify a live website. Verify new-tab behaviour and theme styling on your own test site.

## Development

Run `composer install` inside `simple-mpdf-print-button`. Keep customer templates, WordPress configuration, logs and generated documents out of Git. Bundled libraries retain their own licences.

Repository documentation, pull request descriptions and new commit messages are maintained in English.

## Website content

The product information page is separate WordPress page content in `website/`. Import its XML as a draft through Tools → Import → WordPress, or paste the HTML into a page's code editor. It uses editable Gutenberg blocks and does not depend on a plugin information shortcode. This content is not included in the plugin installation ZIP.

## 4.4.2

Removes the information-page shortcode and its bundled page. The plugin only provides PDF functionality. Existing templates and settings are preserved. The neutral branding and removal of external logo placeholders from 4.4.1 are retained.
