FROM php:8.2-fpm

RUN apt-get update -y \
 && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libreadline-dev \
    libxml2-dev \
    libzip-dev \
    tcpdump  \
    zip \
    zlib1g-dev \
 && docker-php-ext-install intl mbstring mysqli pdo pdo_mysql sockets zip \
 && docker-php-ext-configure intl

RUN apt-get update -y \
 && apt-get install -y --no-install-recommends \
    automake \
    cmake \
    git \
    ssh-client \
    wget

COPY /app /app
RUN chown www-data:www-data /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN cd /app && composer install

USER www-data
WORKDIR /app