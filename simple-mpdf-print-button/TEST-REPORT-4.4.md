# Original test report — 4.4.0

This is an English translation of the report supplied with the original distribution. It is historical documentation, not the result of the current GitHub workflow.

Original environment: WordPress 6.8.9 and PHP 8.3.33 in a temporary WordPress Playground instance.

- 96 WordPress integration checks passed: placement, access control, saving, import/export, duplication and previews; plus colour validation, icons, button sizing, shortcode options, opening/downloading and safe filenames.
- 23 JavaScript assertions passed: existing selectors and search, live colour preview, contrast calculation, corner radius, icon switching and disabling custom colours.
- 15 first-party PHP files passed syntax checks. Modified JavaScript files passed `node --check`.
- Standard PDF, real-content preview and download routes produced valid PDFs. Text extraction confirmed the title and content, and that the PDF button was not printed.
- Download responses used an attachment header with the configured filename. Preview responses remained inline, even with the global download option and an explicit download parameter.
- Read Me and Shortcode Reference were updated; the internal Read Me view was covered by integration testing.

The original colour preview checks used DOM test objects rather than a full browser. A browser installation was unavailable. Verify final colours, hover/focus and dimensions in your theme, including mobile. Save & Test retains native new-tab behaviour; browser preferences ultimately determine opening or downloading.

These checks are not a full security audit or a compatibility guarantee for every theme, PHP version or membership plugin. The bundled mPDF library was unchanged.
