# Task Completion

- For PHP edits, run `php -l` on each changed PHP file.
- For route/controller changes, run `php artisan route:list` scoped with `--name` or `--path` where possible.
- For broader backend changes, run `vendor\bin\phpunit` if the database/test environment is available.
- For Blade-only changes, inspect `git diff --stat` and the relevant diff hunks to ensure no accidental encoding or line-ending churn in large templates.
- After onboarding/memory changes, the user can run `serena memories check` from the project root to validate memory references.