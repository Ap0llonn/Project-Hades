FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip curl libonig-dev libpq-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
