#!/bin/sh
set -e

if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    php /var/www/html/artisan key:generate --force
fi

mkdir -p /var/www/html/storage/app/manuels /var/www/html/storage/app/couvertures
mkdir -p /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache

exec "$@"
