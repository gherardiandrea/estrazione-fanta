# Estrazione Fanta

Applicazione Laravel per estrarre in modo casuale una squadra da una lista predefinita,
senza ripetizioni fino al completamento del ciclo.

## Funzionalita

- Estrazione casuale di una squadra alla volta.
- Nessuna ripetizione finche non finiscono le squadre disponibili.
- Conteggio del numero di estrazioni nel ciclo corrente.
- Conteggio dei cicli completati.
- Reset dello stato di estrazione.
- Interfaccia web con aggiornamento AJAX.

## Stack tecnico

- PHP 8.1+
- Laravel 10
- Blade
- jQuery (via CDN)
- Sessione Laravel (driver file di default)

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

4. (Opzionale) Installa dipendenze frontend:

```bash
npm install
```

5. Avvia il server Laravel:

```bash
php artisan serve
```

6. Apri nel browser:

```text
http://127.0.0.1:8000
```

Nota: per lo stato attuale dell'app non e necessario configurare il database,
perche la logica usa sessione e array in memoria.

## Rotte principali

- `GET /` -> pagina principale con stato corrente
- `POST /estrai` -> estrae una squadra e restituisce JSON
- `POST /reset` -> resetta lo stato e restituisce JSON

## Struttura rilevante

- `app/Http/Controllers/SquadraController.php`: logica di estrazione e reset
- `resources/views/squadre.blade.php`: UI e chiamate AJAX
- `routes/web.php`: definizione rotte web

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

- Lista squadre hardcoded nel controller.
- Stato salvato solo in sessione (non condiviso tra utenti/dispositivi).
- View con CSS/JS inline e dipendenza jQuery da CDN.
- Copertura test minima (solo smoke test base).

## Miglioramenti consigliati

1. Spostare la lista squadre in config o database.
2. Estrarre la logica in un Service dedicato (es. `SquadraExtractorService`).
3. Aggiungere test Feature per i casi principali (`/estrai`, ciclo completo, `/reset`).
4. Migliorare UX: pulsanti disabilitati durante richiesta e gestione errori AJAX.
5. Portare JS/CSS in asset Vite, rimuovendo script/style inline.
6. Valutare endpoint API versionati (`/api/v1/...`) se il frontend cresce.

## Licenza

Questo progetto usa il framework Laravel, distribuito sotto licenza MIT.
