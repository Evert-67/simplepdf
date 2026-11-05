# Simple mPDF Print Button 4.4.0

WordPress-plugin van Evert Beijes / CodePug voor PDF-knoppen en eigen HTML/CSS-templates.

Dit is de hoofdplugin **Simple PDF**, zonder BGM Bestelformulieren of andere uitbreidingen.

## Installeren

Open **Actions → SimplePDF tests**, kies een geslaagde run en download het artifact `simple-mpdf-print-button-4.4.0`. Pak deze download uit en upload de daarin opgenomen plugin-ZIP via **WordPress → Plugins → Nieuwe plugin → Plugin uploaden**. De ZIP bevat mPDF en de lettertypen. Een GitHub-broncode-ZIP bevat die afhankelijkheden niet en is niet direct installeerbaar.

De eigen plugincode is overgenomen uit de bestaande 4.4.0-distributie. mPDF en afhankelijkheden worden met Composer opgebouwd uit de vastgelegde versies en bronreferenties van die distributie; `mpdf/autoload.php` blijft het gebruikte pad.

## Functies

- Templates beheren, dupliceren, importeren en exporteren.
- Save & Test: eerst opslaan, vervolgens preview in een nieuw tabblad.
- Standaardtemplate per berichttype en preview met gekozen inhoud.
- Knopkleuren, pictogrammen, grootte en afronding.
- Openen/downloaden en instelbare PDF-bestandsnamen.

Zie [de handleiding](simple-mpdf-print-button/LEESMIJ-4.4.md).

## GitHub-tests

Op PHP 8.0, 8.3 en 8.4: PHP- en JavaScript-syntax, activering in WordPress 6.8.3, kleurvalidatie, bestandsnamen, veilige headers, preview/download-keuze en echte mPDF-uitvoer met tekstextractie. Het testscript gebruikt echte WordPress-functies. De PDF-test controleert de bibliotheek na pluginactivering; de volledige HTTP-knoproute en het browsergedrag worden hiermee niet getest.

Het meegeleverde [eerdere testverslag](simple-mpdf-print-button/TESTVERSLAG-4.4.md) blijft historische documentatie; de actuele GitHub-resultaten staan onder Actions. De tests versturen geen e-mail en veranderen geen live website. Controleer het nieuwe-tabbladgedrag en de themaopmaak ook op je testsite.

## Ontwikkelen

Voer in `simple-mpdf-print-button` `composer install` uit. Bewaar geen echte templates met klantgegevens, WordPress-configuratie, logs of gegenereerde documenten in Git. Meegeleverde bibliotheken behouden hun eigen licenties.
