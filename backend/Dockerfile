FROM php:8.2-apache


RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip


RUN a2enmod rewrite


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY composer.json composer.lock* ./


RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress || true


COPY . .


RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress


RUN chown -R www-data:www-data /var/www/html/runtime
RUN chmod -R 755 /var/www/html/runtime


RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


RUN mkdir -p /var/www/html/runtime

EXPOSE 80
