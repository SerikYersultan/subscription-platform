# ── Stage 1: Node — build frontend assets ────────────────────────────────────
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ resources/

RUN npm run build

# ── Stage 2: PHP — production image ──────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

# System dependencies
RUN apk add --no-cache \
    bash \
    curl \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Загрузка и установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# копируем только файлы, необходимые для установки зависимостей
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# копируем остальные файлы приложения
COPY . .

# копируем скомпилированные фронтенд-ассеты из первого этапа
COPY --from=frontend /app/public/build public/build

# запускаем оптимизацию автозагрузчика после копирования всех файлов, чтобы учесть все классы
RUN composer dump-autoload --optimize

# хранение и права доступа
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# Cконфигурируем nginx и supervisor
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80 443

CMD ["/entrypoint.sh"]
