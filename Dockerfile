# Usar PHP 8.2 con Apache
FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configurar y instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Exponer puerto 80
EXPOSE 80

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Crear directorios necesarios y establecer permisos
# RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Configurar Apache
# COPY <<EOF /etc/apache2/sites-available/000-default.conf
# <VirtualHost *:80>
#     ServerAdmin webmaster@localhost
#     DocumentRoot /var/www/html/public

#     <Directory /var/www/html/public>
#         AllowOverride All
#         Require all granted
#     </Directory>

#     ErrorLog \${APACHE_LOG_DIR}/error.log
#     CustomLog \${APACHE_LOG_DIR}/access.log combined
# </VirtualHost>
# EOF



# Ejecutar migraciones y luego arrancar Apache en primer plano
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80
