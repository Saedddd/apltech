FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    nginx \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Создаем runtime папку
RUN mkdir -p runtime && chmod 777 runtime


RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader --no-interaction

COPY nginx.conf /etc/nginx/sites-enabled/default
RUN rm -rf /etc/nginx/sites-enabled/default.conf


COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80


CMD ["/entrypoint.sh"]