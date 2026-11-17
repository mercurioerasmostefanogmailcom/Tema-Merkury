# Tema Merkury per MercurioCMS

**Versione estensione:** 1.0.3  
**Tema sorgente:** MERKURY 0.6.2  
**Compatibilità dichiarata:** MercurioCMS 1.0.0

## Installazione

1. Accedere all'amministrazione di MercurioCMS.
2. Aprire **Gestione estensioni** e caricare lo ZIP dell'estensione.
3. Attivare **Tema Merkury**.
4. Aprire la nuova voce **Aspetto Merkury** e seguire la configurazione guidata in quattro passaggi; al termine, le impostazioni avanzate restano disponibili nello stesso pannello.

L’estensione non modifica file del core. MercurioCMS è la fonte unica delle impostazioni condivise: MERKURY le legge dalle chiavi native a ogni richiesta e, quando vengono modificate dal pannello del tema, aggiorna le stesse chiavi. Le preferenze esclusivamente grafiche sono conservate separatamente in `merkury_theme_settings`. Non esiste una copia autonoma del logo o della favicon.

## Disattivazione e reversibilità

Disattivando l'estensione, MercurioCMS non carica più il suo entrypoint: la pagina pubblica torna al template e al CSS originali del CMS. I colori, i testi introduttivi, il nome del sito, il logo e la favicon restano nelle impostazioni native di MercurioCMS. Le opzioni specifiche del layout e le icone delle categorie restano nel record persistente del tema. Una riattivazione recupera l'intera configurazione Merkury. La disinstallazione del plugin rimuove i file secondo il comportamento standard di MercurioCMS; la chiave delle impostazioni resta in database per evitare cancellazioni accidentali e può essere rimossa solo con una procedura amministrativa consapevole.


## Impostazioni condivise e routing

- MercurioCMS mantiene la proprietà di nome del sito, descrizione, testi introduttivi, dodici colori, logo, favicon, homepage assegnata e pagine di destinazione delle categorie.
- Il pannello MERKURY legge i valori nativi correnti e salva nelle stesse chiavi CMS quando l’utente modifica i campi condivisi. Non viene eseguita alcuna riscrittura automatica all’avvio del tema.
- Logo e favicon sono gli stessi file e gli stessi percorsi utilizzati dal CMS; non sono mantenute copie alternative nel JSON del tema.
- Con homepage automatica viene mostrato l’archivio cronologico; se il CMS assegna una pagina alla homepage, viene mostrata quella pagina. Una categoria senza pagina assegnata mostra il proprio archivio; una categoria con pagina assegnata apre quella pagina.
- L’archivio cronologico esclude le pagine statiche assegnate e mantiene paginazione superiore/inferiore e navigazione tra articoli.
- Le preferenze esclusivamente MERKURY (layout, dimensioni testata, ombreggiatura, modalità colore, sidebar, metadati visivi categorie e altre opzioni del tema) restano in `merkury_theme_settings`, senza essere confuse con le impostazioni native.

## Funzioni trasferite

- Identità editoriale blu e tipografia locale Manrope/Literata.
- Testata panoramica con titolo e motto configurabili, immagine, ombreggiatura, altezza e adattamento cover/contain; scritta introduttiva, titolo e descrizione sono sincronizzati con le rispettive impostazioni native del CMS.
- Logo e favicon condivisi con MercurioCMS: il tema utilizza direttamente `site_logo` e `site_favicon`, senza copie separate.
- Simbolo M sostituibile con immagine caricabile dal computer.
- Due impaginazioni: cronologica e fotografica, con schede responsive. Su desktop l’immagine occupa una colonna dedicata e titolo, riassunto e collegamento “Continua a leggere” restano in una colonna separata; su tablet e smartphone la scheda si dispone verticalmente senza sovrapposizioni.
- Palette configurabile con controllo dei dodici campi colore nativi di MercurioCMS, inclusi separatamente sfondi e testi di intestazione e piè di pagina.
- Testata ampia (altezza indicativa 132 px, logo e navigazione più ariosi) oppure compatta (64 px, logo e spaziature ridotti), con anteprima coerente.
- Sidebar con elenco categorie.
- Personalizzazione colore e simbolo delle categorie, con caricamento diretto delle icone; metadati e percorsi delle icone sono conservati nella configurazione persistente del tema.
- Modalità chiara/scura/sistema con superfici e testi adeguati al contrasto; scelta manuale del visitatore memorizzata nel browser.
- Configurazione guidata iniziale in quattro passaggi, ripetibile e seguita da impostazioni avanzate sempre modificabili.
- Testo footer e visualizzazione crediti.
- Anteprima live della homepage reale, con modifiche non salvate e selezione larghezza desktop/tablet/smartphone.
- Backup automatico della configurazione, ripristino dell'ultimo backup, esportazione/importazione JSON e ripristino predefiniti.
- Numero massimo di schede in homepage (1–30).

## Adattamenti necessari rispetto a WordPress

MercurioCMS 1.0.0 gestisce **pagine e categorie**, non il modello editoriale WordPress con articoli, autori, tag, commenti, archivi mensili, immagini in evidenza separate e Query Loop. L'estensione rende quindi le pagine pubblicate come schede editoriali e usa le categorie native del CMS. Non vengono introdotti dati WordPress né dipendenze WordPress.

Le funzioni WordPress senza un equivalente nel CMS non vengono emulate con dati fittizi: archivi per autore/data/tag, commenti, ricerca WordPress, navigazione a blocchi e campi meta WordPress per le categorie non sono disponibili come tali. La sidebar mostra le categorie MercurioCMS. Il limite di elementi in homepage viene applicato alla presentazione delle schede già generate dal CMS; non cambia la query né i dati archiviati.

Le immagini possono essere caricate direttamente dal pannello del tema. I file sono salvati nella cartella condivisa `uploads/images` di MercurioCMS; i campi mantengono anche il percorso dell'immagine attualmente selezionata. Sono accettati JPG, PNG, GIF e WebP (massimo 8 MB); la favicon accetta i formati immagine supportati dal caricamento.

## Sicurezza e dati

- Le impostazioni sono validate prima del salvataggio e codificate come JSON.
- Le azioni amministrative usano la protezione CSRF di MercurioCMS.
- L'anteprima live accetta messaggi solo dalla stessa origine e soltanto in una sessione amministrativa con token valido.
- Nessuna tabella di contenuti viene creata o modificata.
- Nessun file del core viene sovrascritto.
- I font inclusi sono quelli presenti nell'archivio sorgente fornito; i crediti/licenze disponibili sono conservati in `CREDITS-LICENSES.txt`.

## Note versione 1.0.0

La versione 1.0.0 consolida le correzioni raccolte durante il collaudo e ricostruisce la procedura guidata iniziale in quattro passaggi. La procedura può essere saltata e riavviata dal pannello; dopo la prima configurazione, tutte le scelte sono disponibili nelle impostazioni avanzate.

## Verifiche eseguite in fase di confezionamento

- Controllo sintattico PHP degli entrypoint e della pagina amministrativa.
- Test isolato del renderer su un output HTML rappresentativo di MercurioCMS (homepage, navigazione, schede, sidebar e footer).
- Controllo della struttura ZIP e dei file obbligatori del manifest.

Il test isolato non sostituisce una prova d'installazione su un'istanza MercurioCMS con database MySQL/MariaDB e browser reali. Prima dell'uso su un sito operativo, installare una copia di prova e verificare anche attivazione, disattivazione, anteprima live, caricamento dei font e comportamento responsive.


## Note release 1.0.0

- Risolto il layout delle schede di categoria e degli album fotografici: immagine e testo hanno colonne indipendenti su desktop; il layout passa in verticale sui dispositivi più stretti. La visualizzazione smartphone resta verticale.
- Differenziate nettamente le intestazioni ampia e compatta: 132 px contro 64 px, con ridimensionamento coordinato di logo, simbolo e navigazione.
- Aggiornata l’anteprima live affinché rispecchi le nuove dimensioni.


## Correzione conclusiva layout 1.0.0
Su desktop e tablet il tema utilizza la larghezza disponibile con margini laterali di circa 2 cm; la regola interessa testata, contenitore principale, homepage, pagine singole, sidebar e footer. Su smartphone rimane attivo il layout compatto.

### Navigazione dell'archivio

Con homepage automatica, il tema mostra l'archivio cronologico delle pagine pubblicate. Se nelle impostazioni native del CMS è assegnata una pagina alla homepage, viene mostrata quella pagina. Una categoria senza pagina di destinazione mostra il proprio archivio; una categoria con pagina assegnata apre la pagina scelta nel CMS. L'archivio cronologico è paginato sopra e sotto i contenuti; nelle singole pagine sono disponibili i collegamenti all'articolo precedente e successivo. Se la colonna laterale è attiva, include anche l'archivio mensile.

## Versione 1.0.3 — sincronizzazione con MercurioCMS

MercurioCMS è la fonte unica per le impostazioni condivise. Il tema legge i valori nativi a ogni richiesta; non esegue migrazioni o riscritture automatiche all'avvio. Quando si modifica un campo condiviso dal pannello MERKURY, il salvataggio aggiorna la corrispondente chiave nativa CMS:

- i dodici colori nativi sono gestiti dal pannello MERKURY e letti direttamente dal CMS;
- `intro_eyebrow` → `hero_eyebrow_text`, `intro_title` → `hero_title_text`, `intro_text` → `site_description`;
- nome sito → `site_name`; logo → `site_logo`; favicon → `site_favicon`;
- homepage (`home_page_id`) e pagine di destinazione delle categorie (`landing_page_id`) restano gestite esclusivamente dal CMS.

Le preferenze solo grafiche (layout, altezza testata, ombreggiatura, sidebar, modalità colore, testo footer e metadati visuali delle categorie) restano in `merkury_theme_settings`. La sincronizzazione non crea tabelle e non modifica pagine, categorie, utenti o contenuti. Logo, favicon, immagini e icone utilizzano la cartella condivisa `uploads/images`.
