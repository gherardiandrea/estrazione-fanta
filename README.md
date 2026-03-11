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
- Reset ciclo e nuova configurazione
- UI responsive con feedback realtime (info/success/error)

## Stack

- PHP 8.1+
- Laravel 10
- Blade
- Vite + JavaScript vanilla
- SQLite

## Requisiti

- PHP 8.1+
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
- Modello: `app/Models/ExtractionConfig.php`
- Sessione: mantiene solo il token di collegamento al record DB
- Stato persistito: teams, remaining_teams, last_team, draw_number, completed_cycles

## API/Rotte principali

- `GET /`
- `POST /setup`
- `POST /estrai`
- `POST /reset`
- `POST /nuova-configurazione`

## Credibilita repository

Il progetto include:

- Licenza MIT (`LICENSE`)
- CI GitHub Actions (`.github/workflows/ci.yml`)
- Template issue (`.github/ISSUE_TEMPLATE/`)
- Guide community:
	- `CONTRIBUTING.md`
	- `SECURITY.md`
	- `CODE_OF_CONDUCT.md`
- Changelog (`CHANGELOG.md`)

## Known issues

- SQLite su path WSL condivisi con tool Windows puo dare `SQLITE_BUSY`.
- Parte CSS ancora inline in Blade (JS gia separato in asset Vite).

## Roadmap breve

1. Storico completo estrazioni/configurazioni
2. Test unitari su `SquadraExtractorService`
3. Estrazione CSS in asset Vite
4. Preferenze UI persistite (tema)

## Security

- Non committare `.env` o credenziali.
- In caso di vulnerabilita, segui `SECURITY.md`.

## Licenza

Distribuito sotto licenza MIT. Vedi `LICENSE`.
