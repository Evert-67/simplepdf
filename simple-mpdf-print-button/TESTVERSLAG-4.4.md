# Testverslag 4.4.0

Getest met WordPress 6.8.9 en PHP 8.3.33 in een tijdelijke WordPress Playground-omgeving.

- 96 WordPress-integratiecontroles geslaagd: bestaande plaatsing, toegangscontrole, opslag, import/export, dupliceren en previews; plus kleurvalidatie, pictogrammen, knopgrootte, shortcode-opties, openen/downloaden en veilige bestandsnamen.
- 23 JavaScript-asserties geslaagd: de bestaande keuzelijsten en zoekfunctie plus de live kleurpreview, contrastberekening, afronding, pictogramwissel en uitschakelen van aangepaste kleuren.
- 15 eigen PHP-bestanden syntactisch gecontroleerd. De gewijzigde JavaScript-bestanden slagen voor node --check.
- Gewone PDF, preview met echte inhoud en download produceren geldige PDF-bestanden. Tekstextractie bevestigt titel en inhoud en dat de PDF-knop niet wordt afgedrukt.
- De HTTP-header voor downloaden is attachment met de ingestelde bestandsnaam. De preview-header blijft inline, zelfs bij de globale downloadinstelling en een expliciete downloadparameter.
- Read Me en Shortcode Reference zijn bijgewerkt; de interne Read Me-weergave is in de integratietest gecontroleerd.

De kleurpreview is functioneel getest met DOM-testobjecten, niet in een volledige browser. Een browserinstallatie was hier niet beschikbaar. Controleer daarom de uiteindelijke kleuren, hover/focus en afmetingen in je eigen thema, ook op mobiel. Het native nieuwe-tabbladgedrag van Save & Test is behouden; browservoorkeuren bepalen uiteindelijk het openen of downloaden.

Dit is geen volledige beveiligingsaudit of compatibiliteitsgarantie voor alle thema's, PHP-versies en membershipplugins. De meegeleverde mPDF-bibliotheek is niet gewijzigd.
