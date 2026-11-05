# Simple mPDF Print Button 4.4.0

A WordPress plugin by Evert Beijes / CodePug for PDF buttons and custom HTML/CSS templates. This repository contains the Simple PDF main plugin; extensions such as BGM Bestelformulieren are separate.

## Installation

Open **Actions → SimplePDF tests**, select a successful run and download the `simple-mpdf-print-button-4.4.0` artifact. Extract the download and upload the contained plugin ZIP through **WordPress → Plugins → Add New → Upload Plugin**. This ZIP includes mPDF and its fonts. GitHub source archives do not contain these dependencies and are not directly installable.

The plugin code was imported from the existing 4.4.0 distribution. Composer installs the dependency versions and source references recorded in that distribution. The plugin continues to use `mpdf/autoload.php`.

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
