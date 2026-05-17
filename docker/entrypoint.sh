#!/bin/sh
set -e

if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at $DB_HOST..."
    until mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
        sleep 2
    done
    echo "Database ready."
fi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
