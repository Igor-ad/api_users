FROM php:8.3-fpm

RUN apt-get update -y && apt-get install -y \
    nano \
    unzip \
    git \
    libzip-dev \
    zlib1g-dev \
    libicu-dev

RUN docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN echo "error_reporting = E_ALL" > /usr/local/etc/php/conf.d/error_reporting.ini

EXPOSE 8000

CMD ["php-fpm"]
