# Estrazione Fanta

Applicazione Laravel per estrarre casualmente squadre senza ripetizioni fino al
completamento del ciclo.

## Stato progetto

- Stato: MVP stabile
- Target: uso locale/self-hosted
- Persistenza: SQLite

## Funzionalita principali

- Setup iniziale con due modalita:
	- squadre predefinite da config
	- squadre personalizzate inserite dall'utente
- Estrazione casuale di una squadra alla volta
- Nessuna ripetizione fino a fine ciclo
- Contatore estrazioni e cicli completati
- Storico estrazioni collassabile (apri solo quando serve)
- Selettore ciclo storico per visualizzare un ciclo completo
- Cancellazione storico con conferma esplicita
- Reset ciclo e nuova configurazione
- UI responsive con feedback realtime (info/success/error)

## Stack

- PHP 8.2+
- Laravel 11
- Blade
- Vite + JavaScript vanilla
- SQLite

## Requisiti

- PHP 8.2+
- Composer
- Node.js 18+ (consigliato Node 20 LTS)
- npm

Con nvm:

```bash
nvm use
```

## Setup rapido

1. Clona repository

```bash
git clone https://github.com/gherardiandrea/estrazione-fanta.git
cd estrazione-fanta
```

2. Installa dipendenze

```bash
composer install
npm install
```

3. Configura ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Imposta le tue squadre predefinite in locale (non pubblicate su Git):

```text
DEFAULT_TEAMS="Squadra 1|Squadra 2|Squadra 3|Squadra 4"
```

4. Configura SQLite e migra

```bash
touch database/database.sqlite
php artisan migrate
```

Per path con spazi in `.env`, usa sempre virgolette:

```text
DB_CONNECTION=sqlite
DB_DATABASE="/percorso/con spazi/database.sqlite"
DB_FOREIGN_KEYS=true
```

5. Avvia in sviluppo

```bash
npm run dev:all
```

In alternativa separato:

```bash
npm run dev
php artisan serve
```

6. Apri app

```text
http://127.0.0.1:8000
```

## Testing e quality

```bash
php artisan test
npm run build
```

Nota Linux/Ubuntu: se manca `mbstring`.

```bash
sudo apt install php-mbstring
```

## Persistenza nel progetto

- Tabella: `extraction_configs`
- Tabella storico: `extraction_draws`
- Modello: `app/Models/ExtractionConfig.php`
- Modello storico: `app/Models/ExtractionDraw.php`
- Sessione: mantiene solo il token di collegamento al record DB
- Stato persistito: teams, remaining_teams, last_team, draw_number, completed_cycles
- Storico persistito: team estratto, numero estrazione, cicli completati al momento del draw
- Storico mostrato in pagina: pannello "Storico estrazioni" (collassabile), con selettore ciclo

## Privacy squadre predefinite

- Le squadre predefinite non devono stare hardcoded nel repository.
- Il progetto legge i default da `.env` tramite `DEFAULT_TEAMS` (separate da `|`).
- `.env` e gia ignorato da Git, quindi i tuoi nomi restano privati.

## API/Rotte principali

- `GET /`
- `POST /setup`
- `POST /draw`
- `POST /reset`
- `POST /clear-history`
- `POST /new-configuration`

## Credibilita repository

Il progetto include:

- Licenza MIT (`LICENSE`)
- CI GitHub Actions (`.github/workflows/ci.yml`)
- CI eseguibile su push/PR e manualmente da GitHub Actions (Run workflow)
- Template issue (`.github/ISSUE_TEMPLATE/`)
- Guide community:
	- `CONTRIBUTING.md`
	- `SECURITY.md`
	- `CODE_OF_CONDUCT.md`
- Changelog (`CHANGELOG.md`)
- Checklist deploy Laravel Cloud (`docs/laravel-cloud-checklist.md`)

## Known issues

- SQLite su path WSL condivisi con tool Windows puo dare `SQLITE_BUSY`.
- In cloud evitare path SQLite locali: usare database persistente (MySQL/PostgreSQL).
- Parte CSS ancora inline in Blade (JS gia separato in asset Vite).

## Roadmap breve

1. Storico completo configurazioni (oltre allo storico estrazioni)
2. Estrazione CSS in asset Vite
3. Preferenze UI persistite (tema)

## Security

- Non committare `.env` o credenziali.
- In caso di vulnerabilita, segui `SECURITY.md`.

## Licenza

Distribuito sotto licenza MIT. Vedi `LICENSE`.
