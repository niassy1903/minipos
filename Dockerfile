FROM node:22-alpine AS assets

WORKDIR /app

RUN corepack enable

COPY package.json ./
RUN npm install

COPY . .
RUN npm run build


FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-scripts


FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libpq-dev \
        libsqlite3-dev \
        libzip-dev \
        git \
        zip \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_DEBUG=false \
    APP_ENV=production \
    LOG_CHANNEL=stderr \
    PORT=10000

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app/ ./
COPY --from=assets --chown=www-data:www-data /app/public/build/ ./public/build/

RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
        public/build \
    && php artisan package:discover --ansi --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R ug+rwX storage bootstrap/cache public

RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
    /etc/apache2/sites-available/000-default.conf \
    && printf '\n<Directory /var/www/html/public>\n    AllowOverride All\n    Options Indexes FollowSymLinks\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

EXPOSE 10000

CMD sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \\:80>/<VirtualHost *:${PORT}>/" \
        /etc/apache2/sites-available/000-default.conf \
    && exec apache2-foreground
