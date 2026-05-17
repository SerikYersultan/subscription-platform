#!/bin/bash
set -e

DOMAIN="recurly.kz"
EMAIL="olegyugaj61@gmail.com"
CERT_PATH="./certbot/conf/live/$DOMAIN"

echo "==> Checking .env..."
if [ ! -f .env ]; then
    echo "ERROR: .env file not found."
    echo "       Create it: cp .env.example .env  (then fill in APP_KEY, DB passwords)"
    exit 1
fi

APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d= -f2-)
if [ -z "$APP_KEY_VALUE" ]; then
    echo "ERROR: APP_KEY is empty."
    echo "       Run on your local machine:  php artisan key:generate --show"
    echo "       Then paste the result into .env as APP_KEY=base64:..."
    exit 1
fi

if [ ! -f "$CERT_PATH/fullchain.pem" ]; then
    echo "==> Creating temporary self-signed certificate so nginx can start..."
    mkdir -p "$CERT_PATH"
    openssl req -x509 -nodes -newkey rsa:2048 \
        -keyout "$CERT_PATH/privkey.pem" \
        -out    "$CERT_PATH/fullchain.pem" \
        -days 1 -subj "/CN=$DOMAIN" 2>/dev/null
    echo "    Temporary cert created."
fi

echo "==> Building and starting containers..."
docker compose up -d --build

echo "==> Waiting 30 seconds for app to initialize and DB to migrate..."
sleep 30

echo "==> Obtaining SSL certificate from Let's Encrypt..."
docker compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    -d "$DOMAIN" \
    -d "www.$DOMAIN" \
    --email "$EMAIL" \
    --agree-tos \
    --no-eff-email \
    --force-renewal

echo "==> Reloading nginx with the real certificate..."
docker compose exec app nginx -s reload

echo ""
echo "==> Done! Your app is live at: https://$DOMAIN"
