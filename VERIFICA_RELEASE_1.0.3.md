# Verifica tecnica MERKURY 1.0.3

## Modifiche incluse
- Rinominate le cinque voci principali della palette con descrizioni che identificano l'elemento interessato.
- Rinominate le sette voci native aggiuntive specificando se agiscono su schede/riquadri, testata/menu, introduzione o piè di pagina.
- Aggiunta una spiegazione della condivisione dei dodici colori con le impostazioni native MercurioCMS.
- Il pannello mostra l'immagine e il percorso della favicon effettivamente salvata nella chiave nativa `site_favicon`.
- L'anteprima della favicon nel pannello risolve correttamente il percorso dalla radice CMS anche quando la pagina è aperta dentro `/plugins/`.
- Il front-end legge esclusivamente `site_favicon`, elimina eventuali link favicon duplicati e applica un parametro versione basato sulla data del file per evitare cache obsolete.

## Verifiche eseguite
- `php -l admin.php`: superato.
- `php -l plugin.php`: superato.
- Controllo statico delle dodici chiavi colore e delle corrispondenze con le chiavi native CMS: superato.
- Controllo statico della lettura/scrittura della favicon nativa `site_favicon`: superato.
- Verifica struttura e integrità ZIP: superata.

## Limite del collaudo
Non è stato eseguito un test browser sulla specifica installazione locale dell'utente. Dopo l'installazione è quindi necessario verificare dal pannello CMS il cambio favicon, la visualizzazione nella scheda del browser e la corrispondenza dei dodici colori tra Impostazioni MercurioCMS e Tema Merkury.
