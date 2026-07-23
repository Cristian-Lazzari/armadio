# Riepilogo progetto "Armadio" — per Claude Code (VSCode)

## Cos'è
App per pianificare il guardaroba di Cristian: cicli di bucato, asciugatura,
carico lavatrice in kg, guardaroba con capi reali (foto incluse), calendario
mensile con outfit giorno per giorno, salvataggio su MariaDB via Laravel.

## Evoluzione (in ordine)
1. Calcolatore HTML standalone: slider bucato/asciugatura/scorta → quantità
   consigliate per categoria (mutande, calzini, canotte, magliette, pantaloni,
   felpe), dark UI (`#0d1117` / indigo `#6366F1`).
2. Simulazione calendario 30 giorni: rotazione capi, giorni di bucato,
   barra di asciugatura con countdown, card rosse sui giorni scoperti.
3. Calcolo kg per lavaggio con pesi di riferimento reali (GSM tessuti,
   range jeans/felpe documentati), slider capacità lavatrice, avviso
   sovraccarico e avviso "lavatrice mezza vuota".
4. Slider bucato esteso fino a 20 giorni (con avvisi realistici sui carichi).
5. UI ottimizzata mobile: card invece di tabelle, target touch 44px,
   bottoni sticky, stepper e select touch-friendly.
6. Multi-tipo per categoria: calzini/pantaloni/felpe possono avere più
   varianti (es. fantasmini + lunghi, jeans + corti), ognuna con quantità,
   usi e peso propri; sigle dedicate nel calendario (Fa1, Je2, Ho1...).
7. Date reali: data di inizio piano, calendario con giorni della settimana
   veri, "Oggi" evidenziato, date di rientro asciugatura.
8. Persistenza: autosave continuo + programmazioni salvate con nome
   (prima versione: localStorage nel browser).
9. Esclusione stagionale: switch per disattivare felpe/canotte in un
   periodo (es. estate), la simulazione le esclude ovunque (calendario,
   bucati, kg).
10. Guardaroba con capi reali: importato l'ordine H&M di Cristian (11 capi:
    canotte nera/bianca, t-shirt bianca/crema/nera, polo slim/strutturata/
    traforata, camicia bowling salvia, camicia traforata nera, pantaloni
    eleganti neri) con nome, colore modificabile, peso e foto — le foto
    sono state ritagliate dagli screenshot degli ordini e incorporate
    come base64 nei default.
11. Scheda outfit al tocco su un giorno: modal con foto/colore di ogni
    capo indossato, bottone "Cambia ⇄" per scorrere le alternative di
    quel giorno (aggiorna la simulazione a cascata), upload foto per i
    capi generici tramite input file + ridimensionamento canvas.
12. **Porting a Laravel + MariaDB (per MAMP)**: creato pacchetto con
    - `app/Models/Plan.php` (cast `state` ad array, colonna LONGTEXT)
    - `app/Http/Controllers/PlanController.php` (CRUD programmazioni +
      autosave su `/api/plans/auto`)
    - `app/Http/Controllers/GarmentPhotoController.php` (upload foto su
      `storage/app/public/garments`)
    - `database/migrations/..._create_plans_table.php`
    - `resources/views/armadio.blade.php` (stesso frontend, persistenza
      riscritta da localStorage a fetch verso le API Laravel, CSRF via
      meta tag)
    - `routes/web.php` (snippet da incollare, non sostituire il file
      del progetto)
    - `README-installazione.md` con istruzioni MAMP (porta 8889, utente
      root/root, fallback socket, `php artisan migrate`,
      `php artisan storage:link`)
    Consegnato come `armadio-laravel.zip`.

## Stato attuale / bug da risolvere ORA
Cristian ha installato il pacchetto e sta usando **sessioni su database**
(driver `SESSION_DRIVER=database`, di default in molti setup Laravel
recenti), ma **non ha eseguito la migration delle sessioni**. Errore:

```
SQLSTATE[42S02]: Base table or view not found: 1146
Table 'armadio.sessions' doesn't exist
(Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: armadio)
```

Cause probabile e fix da verificare in ordine:
1. Controllare `.env` → `SESSION_DRIVER=database` (o `SESSION_CONNECTION`).
2. Se sì, manca la tabella sessions: generarla e migrare:
   ```bash
   php artisan session:table
   php artisan migrate
   ```
3. Verificare che la migration sia stata eseguita sul database giusto
   (porta 3306 in questo errore, ma il README indicava 8889 tipico di
   MAMP — controllare se ci sono DUE MySQL attivi, es. MAMP su 8889 e un
   MySQL di sistema su 3306, e che `.env` punti a quello con le tabelle
   effettivamente migrate: `php artisan migrate:status` per controllare).
4. In alternativa più semplice per uso locale mono-utente: cambiare
   `SESSION_DRIVER=file` in `.env` per evitare la dipendenza dalla tabella
   sessions, se non serve persistenza sessione su DB.

## File di riferimento consegnati
- `calcolatore-armadio.html` (versione standalone HTML, superata dalla
  versione Laravel ma utile come riferimento per il frontend/JS)
- `armadio-laravel.zip` (pacchetto Laravel corrente, da questo si riparte)

## Prossimo passo
Risolvere l'errore sessions sopra, poi verificare end-to-end:
autosave (`PUT /api/plans/auto`), salvataggio programmazioni con nome,
upload foto capo, e caricamento capi reali H&M già inclusi nei default.
Dopo: valutare regole di abbinamento outfit (evitare combinazioni,
suggerimenti automatici) come prossima feature.