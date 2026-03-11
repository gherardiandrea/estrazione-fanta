# Estrazione Fanta

Applicazione Laravel per estrarre in modo casuale una squadra da una lista predefinita,
senza ripetizioni fino al completamento del ciclo.

## Funzionalita

- Estrazione casuale di una squadra alla volta.
- Nessuna ripetizione finche non finiscono le squadre disponibili.
- Conteggio del numero di estrazioni nel ciclo corrente.
- Conteggio dei cicli completati.
- Reset dello stato di estrazione.
- Interfaccia web moderna e responsive.
- Feedback utente in tempo reale (stato, successo, errore).
- Pulsanti protetti da click multipli durante le richieste.
- Stato esplicito quando la lista squadre e vuota.

## Stack tecnico

- PHP 8.1+
- Laravel 10
- Blade
- JavaScript vanilla con Vite
- Sessione Laravel (driver file di default)

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

5. Avvia Vite (in un terminale separato):

```bash
npm run dev
```

6. Avvia il server Laravel:

```bash
php artisan serve
```

7. Apri nel browser:

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
- `app/Services/SquadraExtractorService.php`: logica di business per estrazione/reset
- `config/squadre.php`: elenco squadre configurabile
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

- Stato salvato solo in sessione (non condiviso tra utenti/dispositivi).
- CSS ancora inline nella view (JS separato su asset Vite).
- Mancano test unitari sul service e test end-to-end browser.

## Miglioramenti consigliati

1. Spostare lo stato da sessione a persistenza su database se serve condivisione multiutente.
2. Aggiungere test unitari dedicati a `SquadraExtractorService`.
3. Portare anche il CSS in asset Vite, rimuovendo gli stili inline dalla view.
4. Introdurre persistenza preferenze UI (es. tema) in localStorage.
5. Valutare endpoint API versionati (`/api/v1/...`) se il frontend cresce.

## Licenza

Questo progetto usa il framework Laravel, distribuito sotto licenza MIT.
