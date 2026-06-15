FROM php:8.2-apache

# Установка расширений PHP для PostgreSQL и MySQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip

# Включение mod_rewrite
RUN a2enmod rewrite

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/runtime
RUN chmod -R 755 /var/www/html/runtime

EXPOSE 80