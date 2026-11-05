# Simple mPDF Print Button 4.4.1

A WordPress plugin by Evert Beijes / CodePug for PDF buttons and custom HTML/CSS templates.

## Installation

**[Download the installable WordPress plugin ZIP](https://github.com/Evert-67/simplepdf/releases/latest/download/simple-mpdf-print-button-4.4.1.zip)**

Upload this ZIP directly through **WordPress → Plugins → Add New → Upload Plugin**. Do not extract it first. If updating, choose to replace the existing plugin.

Use the named plugin ZIP under [Releases](https://github.com/Evert-67/simplepdf/releases/latest). **Code → Download ZIP**, `main.zip`, and the automatic **Source code** downloads are development archives, not WordPress installation packages. The release ZIP includes mPDF and its fonts; the source archive does not.

The plugin code was imported from the existing 4.4.1 distribution. Composer installs the dependency versions and source references recorded in that distribution. The plugin continues to use `mpdf/autoload.php`.

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

## Plugin information page

Create a WordPress page titled **Simple PDF**, add a Shortcode block containing `[simple_pdf_info]`, and set PDF button placement to **None** for that page. The English information page includes features, setup instructions, shortcode examples, a minimal PDF template and frequently asked questions. It never lists or loads your saved templates.

## 4.4.1

Removes site-specific legacy link rewriting and an old example page. Removes the external logo placeholder: `[site_logo]` now returns an empty string when no site logo is configured. Existing database templates and settings remain unchanged. Adds the public `[simple_pdf_info]` information shortcode.
