# Estrazione Fanta

Applicazione Laravel per estrarre in modo casuale una squadra da una lista predefinita,
senza ripetizioni fino al completamento del ciclo.

## Funzionalita

- Configurazione iniziale: scelta tra squadre predefinite o lista personalizzata.
- Estrazione casuale di una squadra alla volta.
- Nessuna ripetizione finche non finiscono le squadre disponibili.
- Conteggio del numero di estrazioni nel ciclo corrente.
- Conteggio dei cicli completati.
- Reset dello stato di estrazione.
- Interfaccia web moderna e responsive.
- Feedback utente in tempo reale (stato, successo, errore).
- Pulsanti protetti da click multipli durante le richieste.
- Stato esplicito quando la lista squadre e vuota.
- Possibilita di avviare una nuova configurazione in qualsiasi momento.

## Stack tecnico

- PHP 8.1+
- Laravel 10
- Blade
- JavaScript vanilla con Vite
- SQLite per persistenza stato/configurazioni

## Requisiti

- PHP 8.1+
- Composer
- Node.js 18+ (consigliato Node 20 LTS)
- NPM

Se usi `nvm`, il progetto include `.nvmrc`.

```bash
nvm use
```

## Avvio rapido

1. Clona il repository:

```bash
git clone https://github.com/gherardiandrea/estrazione-fanta.git
cd estrazione-fanta
```

2. Installa dipendenze PHP:

```bash
composer install
```

3. Crea file ambiente e chiave applicativa:

```bash
cp .env.example .env
php artisan key:generate
```

4. Installa dipendenze frontend:

```bash
npm install
```

5. Avvio comodo (Laravel + Vite insieme):

```bash
npm run dev:all
```

In alternativa, avvio separato.

6. Avvia Vite (in un terminale separato):

```bash
npm run dev
```

7. Avvia il server Laravel:

```bash
php artisan serve
```

8. Apri nel browser:

```text
http://127.0.0.1:8000
```

Nota: lo stato operativo e persistito su SQLite. La sessione mantiene solo il token
del browser corrente, mentre dati di squadre/estrazioni sono su database.

Per la persistenza su SQLite:

```bash
touch database/database.sqlite
```

In `.env`:

```text
DB_CONNECTION=sqlite
DB_DATABASE=/percorso/assoluto/al/progetto/database/database.sqlite
```

Poi esegui le migration:

```bash
php artisan migrate
```

Flusso consigliato:

1. Apri `/`.
2. Scegli modalita `predefinita` o `personalizzata`.
3. Avvia l'estrazione.
4. Se vuoi cambiare elenco, usa il pulsante `Nuova configurazione`.

## Rotte principali

- `GET /` -> pagina principale con stato corrente
- `POST /setup` -> inizializza le squadre (default/custom)
- `POST /estrai` -> estrae una squadra e restituisce JSON
- `POST /reset` -> resetta lo stato e restituisce JSON
- `POST /nuova-configurazione` -> cancella la configurazione corrente e torna alla schermata iniziale

## Struttura rilevante

- `app/Http/Controllers/SquadraController.php`: logica di estrazione e reset
- `app/Models/ExtractionConfig.php`: modello stato/configurazione persistita
- `app/Services/SquadraExtractorService.php`: logica di business per estrazione/reset
- `config/squadre.php`: elenco squadre configurabile
- `database/migrations/2026_03_11_000002_create_extraction_configs_table.php`: tabella persistenza estrazioni
- `resources/views/squadre.blade.php`: layout UI e stile pagina
- `resources/js/squadre.js`: logica frontend (fetch AJAX + aggiornamento DOM)
- `routes/web.php`: definizione rotte web
- `tests/Feature/SquadraControllerTest.php`: test feature del flusso principale

## Esecuzione test

```bash
php artisan test
```

Nel setup attuale, i test possono fallire se l'estensione PHP `mbstring` non e
installata.

Su Ubuntu/Debian:

```bash
sudo apt install php-mbstring
```

## Limiti attuali

- CSS ancora inline nella view (JS separato su asset Vite).
- Mancano test unitari sul service e test end-to-end browser.
- Nessuna persistenza storica delle configurazioni squadre.

Aggiornamento: lo stato operativo corrente e ora persistito su SQLite tramite tabella
`extraction_configs` (setup, squadre restanti, ultima estrazione, cicli).

## Miglioramenti consigliati

1. Aggiungere storico configurazioni/estrazioni (non solo stato corrente).
2. Aggiungere test unitari dedicati a `SquadraExtractorService`.
3. Portare anche il CSS in asset Vite, rimuovendo gli stili inline dalla view.
4. Introdurre persistenza preferenze UI (es. tema) in localStorage.
5. Valutare endpoint API versionati (`/api/v1/...`) se il frontend cresce.

## Licenza

Questo progetto usa il framework Laravel, distribuito sotto licenza MIT.
