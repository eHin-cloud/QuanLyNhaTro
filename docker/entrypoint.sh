#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
        cp .env.docker .env
    else
        cp .env.example .env
    fi
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -f package.json ] && [ ! -d node_modules ]; then
    npm install
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

php artisan config:clear

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
    until mysqladmin ping -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-smartroom}" -p"${DB_PASSWORD:-smartroom}" --silent; do
        sleep 2
    done
fi

php artisan migrate --force
php artisan db:seed --force

exec "$@"
