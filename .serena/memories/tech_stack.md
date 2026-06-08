# Tech Stack

- PHP 8.2+ Laravel 11 project (`composer.json`).
- Frontend assets through Vite 6 and Tailwind 3 (`package.json`, `vite.config.js`, `tailwind.config.js`), though many admin Blade pages use Tailwind CDN and FontAwesome directly.
- Database schema via Laravel migrations in `database/migrations`; Eloquent models in `app/Models`.
- PDF support via `barryvdh/laravel-dompdf`.
- Tests use PHPUnit 11 with Laravel test harness in `tests/`.