# --- Stage 1: install PHP dependencies -------------------------------------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --no-autoloader

COPY . .
# --no-scripts here too: this stage's PHP build doesn't have pdo_pgsql (or
# any of the app's runtime extensions) loaded, and dump-autoload otherwise
# fires the post-autoload-dump script (artisan package:discover) by default.
# entrypoint.sh runs that explicitly in the runtime image instead, where the
# extensions actually match what the app needs.
RUN composer dump-autoload --no-dev --optimize --no-scripts

# --- Stage 2: runtime image --------------------------------------------------
FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        unzip \
        gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql opcache zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=vendor /app ./

COPY docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Laravel needs to write to these at runtime regardless of who the container
# user ends up being (Render runs containers as a non-root UID).
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
