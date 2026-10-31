# STAGE 1: BUILD FRONTEND
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# STAGE 2: LARAVEL
FROM serversideup/php:8.4-fpm-nginx

USER root

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Laravel permissions
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV SSL_MODE=off
ENV NGINX_WEBROOT=/var/www/html/public
ENV PHP_OPCACHE_ENABLE=1

USER www-data

EXPOSE 8080