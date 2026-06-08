# Conventions

- Controllers generally keep request filtering/query composition inline rather than separate query objects.
- Admin web routes are grouped behind `Route::middleware('admin')` in `routes/web.php` and use route names such as `smartroom.admin.*` or `admin.<module>.*`.
- Blade admin UI uses dark Tailwind utility classes, FontAwesome icons, rounded panels, and form submissions with CSRF for mutations.
- Room/resident update paths include defensive validation and optimistic-locking `version` checks in existing code; keep those patterns when editing the same flows.
- Utility invoices are represented by `UtilityRecord`; status values are `sent`, `paid`, `overdue`, with synthetic `draft` rows in `PaymentController` for rooms without current-month records.