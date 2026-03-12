# Laravel Cloud Deployment Checklist

## 1. Versioni e dipendenze

- [ ] `composer.json` su Laravel 11 (`laravel/framework:^11.0`)
- [ ] PHP richiesto `^8.2`
- [ ] `composer.lock` committato
- [ ] `npm run build` eseguito senza errori

## 2. Configurazione applicazione

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` valorizzata
- [ ] `APP_URL` impostata al dominio cloud

## 3. Database (consigliato MySQL/PostgreSQL)

- [ ] Evitare SQLite su filesystem effimero cloud
- [ ] Configurare `DB_CONNECTION` su database persistente
- [ ] Impostare `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] Eseguire migrazioni in fase di deploy (`php artisan migrate --force`)

## 4. Sessioni, cache e queue

- [ ] `SESSION_DRIVER=database` o `redis` (non `file` in ambienti multi-instance)
- [ ] `CACHE_DRIVER=database` o `redis`
- [ ] `QUEUE_CONNECTION=database` o `redis`
- [ ] Se usi `database` queue/session, verificare tabelle migrate

## 5. Build e runtime

- [ ] Build command: `composer install --no-interaction --prefer-dist --optimize-autoloader && npm ci && npm run build`
- [ ] Post-build: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] Start command conforme alla piattaforma (tipicamente php-fpm + web server gestito)

## 6. Storage e permessi

- [ ] Configurare storage persistente se necessario
- [ ] Eseguire `php artisan storage:link` se servono file pubblici
- [ ] Non affidarsi a file locali per dati critici

## 7. Variabili progetto specifiche

- [ ] `DEFAULT_TEAMS` impostata (formato `Team A|Team B|Team C`)
- [ ] Verificare route base `/` raggiungibile
- [ ] Verificare setup iniziale (`/setup`) e draw/reset da UI

## 8. Smoke test post-deploy

- [ ] Aprire home page
- [ ] Configurare squadre (default/custom)
- [ ] Eseguire 2-3 estrazioni
- [ ] Verificare storico per ciclo
- [ ] Verificare reset ciclo
- [ ] Verificare cancellazione storico

## 9. Sicurezza minima

- [ ] Nessun segreto hardcoded nel repository
- [ ] `.env` non versionato
- [ ] HTTPS attivo
- [ ] Log monitorati dopo il primo deploy

## 10. Rollback rapido

- [ ] Tenere il commit precedente pronto
- [ ] Se deploy fallisce, rollback all'ultima release stabile
- [ ] Ripetere migrate solo dopo verifica connessione DB
