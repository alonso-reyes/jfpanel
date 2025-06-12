# Etapa de construcción: solo instala dependencias
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Etapa principal con Apache + PHP 8.2
FROM php:8.2-apache

WORKDIR /var/www/html

# Habilita mod_rewrite para Laravel
RUN a2enmod rewrite

# Copia la app y vendor
COPY . .
COPY --from=vendor /app/vendor ./vendor

# Establece permisos correctos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expone el puerto 80
EXPOSE 80
