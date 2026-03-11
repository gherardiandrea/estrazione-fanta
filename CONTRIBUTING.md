# Contributing

Thanks for your interest in improving Estrazione Fanta.

## Setup

1. Fork and clone the repository.
2. Install dependencies:
   - `composer install`
   - `npm install`
3. Create env file and key:
   - `cp .env.example .env`
   - `php artisan key:generate`
4. Configure SQLite and run migrations:
   - `php artisan migrate`

## Development

- Use Node 20 (`nvm use`) before running frontend commands.
- Run app and Vite together with `npm run dev:all`.
- Keep changes focused and small.

## Quality checks

Before opening a PR, run:

- `php artisan test`
- `npm run build`

## Pull requests

- Use clear titles and descriptions.
- Mention related issue(s).
- Include screenshots for UI changes.
- Update `README.md` when behavior/setup changes.
