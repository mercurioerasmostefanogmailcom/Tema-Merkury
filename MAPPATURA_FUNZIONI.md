# Mappatura funzionale MERKURY 0.6.2 → MercurioCMS 1.0.0

| Funzione nel tema sorgente | Adattamento MercurioCMS | Stato |
|---|---|---|
| Identità editoriale, palette blu | CSS dedicato e variabili configurabili | Trasferita |
| Font Manrope e Literata | File WOFF2 inclusi nell'estensione | Trasferita |
| Testata panoramica, titolo e motto | Impostazioni tema e renderer homepage; testo Merkury prioritario anche con homepage CMS impostata su una pagina | Trasferita e corretta in 0.2.1 |
| Immagine testata, overlay, altezza e fit | Percorso media o URL fornito dall'amministratore | Trasferita con selezione manuale del percorso |
| Simbolo M sostituibile | Immagine da percorso media/URL | Trasferita con selezione manuale del percorso |
| Preset cronologico/fotografico | Layout delle schede pagina | Adattata a pagine CMS |
| Numero elementi homepage | Limite di schede renderizzate | Trasferita sul livello di presentazione |
| Sidebar categorie | Elenco delle categorie visibili del CMS | Adattata |
| Colore e icona categoria | Metadati visuali salvati nella configurazione Merkury | Trasferita |
| Testata ampia/compatta | Classe e altezza header | Trasferita |
| Colore header/footer | Colore comune configurabile | Trasferita |
| Modalità chiara/scura/sistema | JavaScript locale e preferenza browser; stato applicato a HTML e body e palette scura completa | Trasferita e corretta in 0.2.1 |
| Testo footer e crediti | Impostazioni tema | Trasferita |
| Backup automatico configurazione | Backup JSON precedente al salvataggio | Trasferita |
| Esporta/importa JSON | Formato `merkury-settings` | Trasferita |
| Ripristino backup e predefiniti | Azioni amministrative protette da CSRF | Trasferita |
| Anteprima live homepage | Iframe della homepage effettiva, token CSRF e messaggi same-origin | Trasferita, da collaudare nel browser dell'installazione |
| Procedura guidata iniziale in quattro passaggi | Procedura Merkury a quattro passaggi con salvataggio progressivo e accesso alle impostazioni avanzate | Trasferita in 0.2.1 |
| Archivi WordPress per autore, data e tag | Non esistono nel modello pagine/categorie di MercurioCMS | Non applicabile senza estendere il CMS |
| Commenti, tag, autori e navigazione blocchi | Non presenti nel CMS sorgente | Non applicabile |
| Ricerca WordPress, Query Loop, paginazione WordPress | Non esiste un equivalente nel frontend sorgente CMS | Non trasferita |
| Immagine in evidenza WordPress distinta dal contenuto | L’immagine di sfondo della pagina viene mostrata in un riquadro nella scheda, accanto a titolo, riassunto e collegamento | Adattamento completato in 0.2.1 |
| Libreria Media WordPress nel pannello | Non esiste un selettore media pubblico riutilizzabile per le estensioni CMS | Percorso inserito manualmente |

La tabella distingue il trasferimento effettivo dalle funzioni non disponibili nel modello dati di MercurioCMS. Non vengono creati contenuti fittizi per simulare le funzioni mancanti.


## MERKURY 1.0.3 — proprietà delle impostazioni

Le chiavi native di MercurioCMS sono la fonte unica per nome, descrizione, testi introduttivi, colori, logo e favicon. Il tema le legge a ogni richiesta e scrive sulle stesse chiavi soltanto in seguito a un salvataggio esplicito dal proprio pannello. Non esegue più sincronizzazioni automatiche in fase di caricamento. `home_page_id` e `categories.landing_page_id` restano gestiti esclusivamente dal CMS e sono rispettati dal routing pubblico. Le preferenze specifiche del tema restano nel JSON `merkury_theme_settings`.
