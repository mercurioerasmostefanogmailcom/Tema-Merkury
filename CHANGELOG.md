## 1.0.3
- Chiariti i nomi di tutti i selettori della palette, specificando dove agisce ogni colore (inclusi gli effetti al passaggio sui link).
- Mostrata la favicon nativa attualmente impostata in MercurioCMS nella configurazione del tema.
- La pagina pubblica usa esclusivamente la favicon nativa del CMS, rimuove i riferimenti duplicati e aggiorna la cache quando il file cambia.

# Changelog MERKURY

## 1.0.2
- Corretto il modello di sincronizzazione: le impostazioni native MercurioCMS sono lette dal CMS a ogni richiesta; il tema non esegue più una migrazione automatica che poteva sovrascriverle all'attivazione.
- Il pannello MERKURY scrive nelle chiavi native condivise per colori, nome del sito, descrizione e testi introduttivi.
- Logo e favicon utilizzano esclusivamente i campi nativi `site_logo` e `site_favicon` del CMS; rimossi gli archivi duplicati del tema.
- Aggiunta la gestione dei dodici colori nativi del CMS, compresi header, footer e area introduttiva.
- Corretto il routing: l'archivio cronologico viene mostrato solo quando il CMS non ha risolto una pagina assegnata a homepage o categoria.
- Mantenute le preferenze specifiche MERKURY nel record persistente `merkury_theme_settings`.

## 1.0.0 — Release definitiva per MercurioCMS 1.0.0
- Aggiunta la seconda barra di paginazione in fondo all’elenco delle pagine, mantenendo quella superiore; i collegamenti e lo stato della navigazione restano identici a quelli generati dal CMS.
- Corretta la larghezza complessiva del sito: su desktop e tablet contenitori, testata, homepage, pagine e footer si estendono fino a mantenere circa 2 cm di margine laterale, eliminando l’ampia fascia vuota ai lati. Su smartphone resta il margine compatto già previsto.
- Corretto il layout desktop/tablet delle schede di categoria e degli album fotografici: immagine in colonna dedicata, contenuti in colonna separata e nessuna sovrapposizione; impaginazione verticale sui dispositivi più stretti.
- Distinte maggiormente le modalità testata ampia (132 px) e compatta (64 px), ridimensionando in modo coerente logo, simbolo e navigazione.
- Allineata l’anteprima live alle nuove dimensioni e aggiornata la documentazione.

# Registro modifiche — Tema Merkury per MercurioCMS

## 0.2.1 — Correzioni dopo il collaudo 0.2.0
- Ricostruita la testata panoramica anche quando la homepage del CMS è configurata per mostrare una pagina specifica; il testo impostato in Merkury sostituisce quello introduttivo standard del CMS.
- Corretto il caricamento delle immagini del tema e delle immagini pagina usando URL assoluti rispetto alla radice effettiva del CMS, anche in installazioni in sottocartella.
- Resa effettiva l'ombreggiatura della testata con livelli sovrapposti e contenuto sempre in primo piano.
- Chiarite le modalità di adattamento: riempimento con eventuale ritaglio (cover) e immagine interamente visibile con eventuali bande (contain).
- Ricostruite le schede delle pagine con immagine in riquadro a sinistra e titolo, riassunto e collegamento “Continua a leggere” a destra.
- Corretto il cambio di modalità chiara/scura applicando lo stato sia all'elemento HTML sia al body e adeguando sfondi, superfici, testi, collegamenti, testata e footer.
- Ripristinata la configurazione guidata in quattro passaggi: stile, testata, colori e preferenze finali; le scelte restano modificabili dalle impostazioni avanzate.
- Aggiornati versione, documentazione e verifiche statiche.

## 0.2.0 — Correzioni dopo il collaudo
- Corretti i percorsi di navigazione dell'area amministrativa usando la directory admin di MercurioCMS come base per i collegamenti del menu.
- Migliorata la leggibilità del pannello di configurazione e dei controlli colore.
- Aggiunto il caricamento diretto di logo, favicon, immagine panoramica, simbolo e icone delle categorie.
- Aggiunto il salvataggio effettivo delle personalizzazioni per categoria, con descrizioni esplicative dei campi.
- Ricostruita la colonna laterale delle categorie come layout a due colonne nella homepage e nelle pagine di elenco.
- Corretto il contrasto della modalità scura: le variabili cromatiche ora si applicano anche al contenitore body con stili inline.
- Aggiornati i riferimenti di versione e la documentazione.

## 0.1.0 — Prima versione adattata per MercurioCMS
- Adattata l'identità grafica MERKURY 0.6.2 al frontend di MercurioCMS 1.0.0.
- Integrati localmente i font Manrope e Literata forniti nell'archivio sorgente.
- Aggiunti preset cronologico/fotografico, testata panoramica e palette personalizzabile.
- Aggiunti impostazioni del footer, modalità colore, categorie con colore/simbolo, backup e gestione JSON.
- Aggiunta anteprima live autenticata, con aggiornamento delle modifiche non salvate e larghezze responsive.
- Nessuna modifica ai file del core o alle tabelle dei contenuti.

## MERKURY 1.0.0 — correzione navigazione archivio
- La homepage del tema mostra sempre l'archivio cronologico delle pagine pubblicate, evitando che una pagina statica selezionata nelle impostazioni CMS (ad esempio “Castello di Padernello”) sostituisca l'elenco.
- Aggiunta paginazione reale superiore e inferiore con parametro dedicato, conteggio totale e mantenimento dei filtri.
- Aggiunta navigazione cronologica dentro ogni pagina: articolo precedente, articolo successivo e Home/ultimi articoli.
- Aggiunto nel menu laterale l'archivio per mese e anno, con conteggio degli articoli e filtro del periodo.

### Rifinitura MERKURY 1.0.0 — menu archivio laterale
- Migliorato il contrasto del menu categorie/archivio mensile in modalità scura, inclusi conteggi, hover e mese selezionato.
- Il menu laterale resta sticky su desktop ma si posiziona sotto la testata: offset dedicato per testata ampia (132 px) e compatta (64 px).
- Su tablet e smartphone il menu torna nel flusso normale, evitando sovrapposizioni e problemi di spazio.


## 1.0.1 — Integrazione impostazioni CMS
- Sincronizzati i colori Merkury con i campi colore nativi di MercurioCMS.
- Sincronizzati titolo e descrizione introduttiva, logo e favicon con le impostazioni generali del CMS.
- Salvati anche nelle impostazioni CMS i riferimenti a immagine panoramica, simbolo, testo footer, modalità colore, layout e icone/metadati delle categorie.
- Applicata la sincronizzazione a configurazione guidata, salvataggio, importazione, backup e ripristino.
- La disattivazione del tema non fa perdere le personalizzazioni condivise.
