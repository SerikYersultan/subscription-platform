#!/bin/sh

set -e

mkdir -p \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache \
    /var/log/supervisor \
    /tmp/nginx_client_body

chmod -R 775 \
    storage \
    bootstrap/cache \
    /var/log/supervisor || true

chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    /tmp/nginx_client_body || true

chown -R www-data:www-data /var/lib/nginx/tmp
chmod -R 775 /var/lib/nginx/tmp

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at $DB_HOST..."

    until mysqladmin ping \
        --ssl=0 \
        -h"$DB_HOST" \
        -P"${DB_PORT:-3306}" \
        -u"$DB_USERNAME" \
        -p"$DB_PASSWORD" \
        --silent; do
        sleep 2
    done

    echo "Database ready."
fi

php artisan config:clear
php artisan cache:clear

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf