# Simple mPDF Print Button 4.4.0

Algemene WordPress-plugin van Evert Beijes / Codepug.

## Installeren en bijwerken

1. Bewaar de bestaande plugin-ZIP en maak een databaseback-up (templates en instellingen staan in de database).
2. Upload deze ZIP via Plugins → Nieuwe plugin → Plugin uploaden. Kies om de bestaande plugin te vervangen.
3. Open Simple PDF Print → Settings en controleer de instellingen. Er worden geen bestaande templates of opties verwijderd.
4. Test één bestaande pagina met een bestaande template.

Vereist: WordPress 6.3 of hoger, PHP 8.0 of hoger en de PHP-extensies mbstring en gd. De meegeleverde mPDF-versie is ongewijzigd; de dependencyverklaring noemt PHP 8.0–8.4. PHP 8.3 is de gekozen integratietestversie. Dit is geen certificering voor alle toekomstige PHP- of WordPress-versies.

## Waar komt de knop?

Via Settings → Button position:
- Before / After / Both: boven, onder of beide.
- Floating bottom left / right: zwevend linksonder of rechtsonder.
- None: alleen handmatige knoppen via shortcode of blok.

Automatic placement on bepaalt de berichttypen. Zonder eerder opgeslagen instelling zijn openbare berichttypen aangevinkt. Een lege selectie schakelt automatische plaatsing overal uit.

In de editor staat het vak PDF button placement. Kies daar de positie en template voor dit specifieke bericht of deze pagina. Use global setting volgt de algemene instellingen. Het berichttype moet voor automatische plaatsing ingeschakeld zijn.

Een zwevende knop kan met een chat-, cookie- of andere zwevende knop botsen: kies dan een andere hoek of normale plaatsing. De zwevende knop vereist dat het thema wp_footer() uitvoert.

## Handmatig plaatsen

Bestaande shortcode blijft werken:

    [print_pdf_button template="123" label="Download PDF"]

Met uitlijning, breedte en eigen klasse:

    [print_pdf_button template="123" label="Download PDF" align="right" width="250px" class="my-button"]

Met een templatekeuzelijst:

    [print_pdf_button template="0" label="Create PDF" align="center"]

Vervang 123 door het template-ID. Een expliciete post_id blijft ondersteund. De bezoeker moet die broninhoud mogen lezen.

In Gutenberg: voeg het blok PDF button toe. Kies de template, tekst, uitlijning en breedte via de blokinstellingen. Het editorvoorbeeld toont de knopvorm; de frontend gebruikt de echte templatekeuze en het thema. Plaats het blok of de shortcode bijvoorbeeld in een kolom. Voorkom een extra automatische knop door de paginapositie op None te zetten.

Breedtes: auto, px, %, em, rem en vw. CSS-functies zoals calc() worden niet geaccepteerd. De bestaande frameworkkeuze gebruikt de CSS van het actieve thema; de plugin downloadt geen frameworks. Voor selectorstijlen blijven de bestaande globale CSS-classes gelden. De class-instelling op de shortcode wordt op de knop toegepast, ook wanneer een keuzelijst wordt gebruikt.

## Templates en toegang

- Templates gebruiken HTML, mPDF-CSS en WordPress-shortcodes. PHP-code in een template wordt niet uitgevoerd.
- Save & Test PDF slaat het klassieke templateformulier eerst op en opent vervolgens een beveiligde voorbeeld-PDF in een nieuw tabblad (of venster, afhankelijk van je browserinstellingen). De editor blijft in het oorspronkelijke tabblad open. De knop gebruikt de normale WordPress-formulieropslag met formtarget="_blank". Gewoon Update/Publish blijft in het eigen tabblad. Omdat de editor niet herlaadt, kan WordPress bij verlaten nog een melding over wijzigingen tonen; herlaad de editor na een geslaagde opslag als je de actuele opgeslagen status wilt zien.
- Preview saved template opent de laatst opgeslagen template in een nieuw tabblad.
- Templatecode kan alleen door beheerders worden opgeslagen. Gepubliceerde templates zijn bruikbaar voor bezoekers die de broninhoud mogen lezen.
- Privéberichten, concepten en wachtwoorden worden gecontroleerd bij het PDF-verzoek. Een berichtwachtwoord moet op de oorspronkelijke pagina worden ingevoerd.
- Require login geldt ook voor rechtstreeks opgevraagde PDF-links.
- Lidmaatschapsplugins met eigen toegangsregels kunnen via smppb_allow_pdf extra blokkeren. Automatische compatibiliteit met iedere membershipplugin wordt niet verondersteld.

Voorbeeld voor ontwikkelaars:

    add_filter('smppb_allow_pdf', function ($allowed, $post, $user_id) {
        return $allowed && my_membership_can_read($user_id, $post->ID);
    }, 10, 3);

Vervang my_membership_can_read door de echte API van de betreffende membershipplugin.

Nieuwe knoppen gebruiken de WordPress admin-post route. Oude directe generate-pdf.php-links worden bij een standaard WordPress-mapstructuur doorgestuurd. Bij een verplaatste wp-content-map of afwijkende installatie moeten oude handgemaakte links opnieuw worden aangemaakt. Shortcodes bouwen zelf de juiste nieuwe URL.

De bestaande beymancloud.nl-linkverwijdering is behouden voor compatibiliteit. Uitschakelen kan met:

    add_filter('smppb_strip_legacy_links', '__return_false');

## Reparaties

- Save & Test slaat daadwerkelijk op en gebruikt een beveiligd voorbeeldverzoek.
- Meerdere keuzelijsten hebben unieke ID's en onafhankelijke beschrijvingen.
- Breedte van vaste shortcodeknoppen wordt met CSS toegepast.
- Knoppen en het nieuwe blok worden onderdrukt tijdens PDF-opbouw.
- Hoofdmenu heeft een werkend templateoverzicht.
- Templatecode bewaart backslashes; editor gebruikt HTML-modus.
- mPDF wordt pas geladen voor PDF-generatie; ontbrekende extensies geven een beheerdersmelding.
- PDF-fouten geven een leesbare melding; technische details alleen in de debuglog als WP_DEBUG actief is.
- Bestaande templates, categorieën, shortcodes en instellingen blijven in de database behouden.

## Nieuw in 4.4: knopopmaak en PDF-uitvoer

In Settings staat Button appearance & PDF output. Schakel Custom button colours in voor achtergrond-, tekst-, rand- en hoverkleuren. De afronding loopt van 0 tot 40 pixels. Zonder inschakelen houdt de knop de bestaande thema-opmaak.

- Grootte: theme, small, normal of large. Grootte werkt onafhankelijk van eigen kleuren.
- Pictogram: none, document, download of print. De pictogrammen worden meegeleverd; er is geen externe iconenbibliotheek nodig.
- Live voorbeeld: kleuren en hover/focus zijn direct te bekijken. De contrastcontrole vergelijkt de gekozen tekst- en achtergrondkleuren; streef naar minimaal 4,5:1. Controleer daarnaast je eigen thema en mobiele weergave.
- PDF behaviour: openen in nieuw tabblad of downloaden. Een downloadknop gebruikt het huidige tabblad als aanvraagdoel om een overbodig leeg tabblad te vermijden. Browserinstellingen kunnen het uiteindelijke gedrag beïnvloeden.
- Save & Test blijft altijd een inline preview in een nieuw tabblad, ook als downloaden de algemene instelling is.
- PDF filename: bijvoorbeeld {site}-{title}-{date}. Tokens: {title}, {id}, {date}, {site}, {template}. Datum is de generatiedatum in de tijdzone van de website. De extensie .pdf wordt automatisch toegevoegd. Onbekende tokens of een leeg patroon vallen terug op {title}; de maximale patroonlengte is 180 tekens. Onveilige bestandsnaamtekens worden verwijderd.

Nieuwe shortcode-opties:

    [print_pdf_button template="123" label="Download PDF" icon="download" output="download" size="large"]

    [print_pdf_button template="123" label="Open PDF" icon="document" output="inline" size="normal"]

icon: inherit, none, document, download, print. output: inherit, inline, download. size: inherit, theme, small, normal, large. Bij weglaten volgt de knop de algemene instellingen. Deze keuzes staan ook in het Gutenberg-blok. Kleuren en bestandsnaampatroon zijn globaal; ze zijn geen shortcode-attributen.

Deze instellingen veranderen de websiteknoppen. De opmaak van de PDF zelf blijft uit de HTML/CSS-template komen. Downloadinstellingen omzeilen geen leesrechten.

De Read Me-pagina in het WordPress-menu is volledig bijgewerkt met inhoudsopgave, versiehistorie en probleemoplossing. Ook Shortcode Reference is bijgewerkt en gecorrigeerd.

## Nieuw in 4.3: templates beheren

- Dupliceren: klik Duplicate bij een gepubliceerde template in het hoofdmenu, of Duplicate template bij een template in Manage all templates. De kopie is een concept met dezelfde HTML/CSS, omschrijving en categorieën. De naam krijgt “(copy)”.
- Export: kies maximaal 100 templates in het hoofdmenu en klik Export selected JSON. Ctrl/Cmd selecteert meerdere items. Ook concepttemplates kunnen worden meegenomen.
- Import: upload een export-JSON van maximaal 5 MB. Alle geïmporteerde templates worden nieuwe concepten, nooit vervangingen. Beoordeel en publiceer ze via Manage all templates.
- Een template mag maximaal 1 MB code bevatten; maximaal 500 categorieën per bestand. Het hele bestand wordt gecontroleerd vóór import. Bij een fout tijdens import worden de daarbij nieuw aangemaakte templates en categorieën opgeruimd.
- Categorieën met dezelfde slug worden hergebruikt zonder hun bestaande naam of plaats in de boom te wijzigen. Nieuwe categorieën krijgen de geëxporteerde hiërarchie.
- Afbeeldingen, lettertypen en andere gekoppelde bestanden worden niet ingebed in de JSON. Pas hun URL's aan of verhuis de bestanden zelf als de oorspronkelijke website verdwijnt. Dit is een template-export, geen volledige websiteback-up.
- Template-ID's, pagina-instellingen, voorbeeldbericht-ID's en website-instellingen worden niet overgezet. Kies na import de juiste standaardtemplates op de nieuwe website.

## Voorbeeld met echte inhoud

In de template-editor staat PDF preview content. Zoek vanaf twee tekens naar berichten, pagina's of andere openbare berichttypen. Kies een resultaat, of voer rechtstreeks een bericht-ID in. ID 0 gebruikt voorbeeldtekst.

Save & Test slaat ook deze selectie op en opent de PDF in een nieuw tabblad. Preview saved template gebruikt de opgeslagen templatecode met de momenteel gekozen bron. Leesrechten en wachtwoordcontrole blijven gelden: een ontoegankelijke bron geeft een melding; de template kan dan wel al opgeslagen zijn. Zoekresultaten tonen maximaal 30 leesbare items.

## Standaardtemplate per berichttype

In Settings → Template per post type kun je bijvoorbeeld aparte templates instellen voor berichten, pagina's en producten. Alleen gepubliceerde templates zijn selecteerbaar.

Voor automatische knoppen geldt: expliciete pagina-instelling → instelling van het berichttype → globale standaard. “Template selector” is ook een expliciete keuze. “Use global setting” bij een pagina betekent: volg de standaardketen, dus eerst het berichttype. Een handmatige shortcode of een PDF button-blok houdt zijn eigen templatekeuze.

## Praktijktest op jouw website

Controleer na bijwerken de knop boven/onder/beide, zwevende posities op mobiel, je eigen CSS-framework en een PDF met logo, tabellen en meerdere pagina's. Controleer ook uitgelogde bezoekers en eventuele membershipregels. Thema-specifieke weergave en bestaande maatwerktemplates moeten op de eigen site worden beoordeeld.
