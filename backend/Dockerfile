FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    nginx \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader --no-interaction

# Создаем runtime папку
RUN mkdir -p runtime && chmod 777 runtime

# Копируем конфиг Nginx
COPY nginx.conf /etc/nginx/sites-enabled/default

# Стартуем PHP-FPM и Nginx
CMD php-fpm -D && nginx -g 'daemon off;'

EXPOSE 80