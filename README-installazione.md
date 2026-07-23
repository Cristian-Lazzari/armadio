# Armadio — installazione su Laravel + MariaDB (MAMP)

App per pianificare il guardaroba: ciclo del bucato, capi reali con foto,
calendario mensile con outfit, salvataggio programmazioni su database.

## Cosa contiene questo pacchetto

```
app/Models/Plan.php
app/Http/Controllers/PlanController.php
app/Http/Controllers/GarmentPhotoController.php
database/migrations/2026_07_21_000000_create_plans_table.php
resources/views/armadio.blade.php
routes/web.php               <- SNIPPET di rotte da incollare, non sostituire il tuo file
```

## Installazione (10 minuti)

### 1. Progetto Laravel
Se non hai già un progetto dove ospitarla:
```bash
composer create-project laravel/laravel armadio
cd armadio
```
Se la aggiungi a un progetto esistente, salta questo passo.

### 2. Copia i file
Copia le cartelle del pacchetto dentro il progetto rispettando i percorsi
(`app/`, `database/`, `resources/`). Per le rotte: **apri `routes/web.php`
del pacchetto e incolla il blocco nel TUO `routes/web.php`** (non
sovrascrivere il file, contiene solo lo snippet).

### 3. Database su MAMP
Avvia MAMP, poi da phpMyAdmin (o CLI) crea il database:
```sql
CREATE DATABASE armadio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Nel file `.env` del progetto:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889          # porta MySQL/MariaDB di MAMP (3306 se hai cambiato le default)
DB_DATABASE=armadio
DB_USERNAME=root
DB_PASSWORD=root      # credenziali default di MAMP
```

> Nota MAMP: se `php artisan migrate` non si connette, prova
> `DB_HOST=localhost` con `DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock`.

### 4. Migrazione e storage
```bash
php artisan migrate
php artisan storage:link    # serve per le foto dei capi
```

### 5. Avvio
```bash
php artisan serve
```
Apri **http://127.0.0.1:8000/armadio** — fine.

In alternativa puoi puntare un host virtuale MAMP alla cartella `public/`
del progetto e usare il webserver di MAMP.

## Come funziona la persistenza

- **Autosave**: ogni modifica viene salvata (con debounce di 0,7 s) nella
  riga `_auto` della tabella `plans` via `PUT /api/plans/auto`.
  Riapri la pagina e ritrovi tutto, da qualsiasi browser/dispositivo.
- **Programmazioni con nome** ("Estate", "Inverno"...): righe della stessa
  tabella, gestite dai bottoni Salva/Elimina in alto.
- **Foto dei capi**: ridimensionate lato client (max 360 px) e caricate in
  `storage/app/public/garments/`; nel database viene salvato solo l'URL.
  Le 11 foto dell'ordine H&M sono incorporate come base64 nei default e
  funzionano senza upload.

## API

| Metodo | Rotta                | Uso                                  |
|--------|----------------------|--------------------------------------|
| GET    | /api/plans           | elenco programmazioni (id, name)     |
| GET    | /api/plans/auto      | stato autosalvato corrente           |
| PUT    | /api/plans/auto      | aggiorna autosave `{state}`          |
| POST   | /api/plans           | salva con nome `{name, state}`       |
| GET    | /api/plans/{id}      | carica una programmazione            |
| DELETE | /api/plans/{id}      | elimina                              |
| POST   | /api/garment-photo   | upload foto capo → `{url}`           |

Le rotte stanno nel gruppo web, quindi il CSRF è gestito dal meta tag già
presente nella Blade. Nessuna autenticazione: uso locale mono-utente. Se un
domani la metti online, avvolgi le rotte in `Route::middleware('auth')`.

## Note tecniche

- La colonna `state` è `LONGTEXT` con cast `array` sul model: JSON su
  MariaDB senza sorprese (su MariaDB `JSON` è comunque un alias di LONGTEXT).
- Compatibile Laravel 10 e 11.
- Tutto il frontend è in un'unica Blade senza build step: niente npm,
  niente Vite, la modifichi e ricarichi.
