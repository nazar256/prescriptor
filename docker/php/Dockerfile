FROM php:7.4.0-fpm
ADD install-composer.sh install-composer.sh
RUN apt-get update && \
    apt-get install -y wget git unzip zlib1g-dev libncurses5-dev libmemcached-dev libcurl4-openssl-dev && \
    rm -rf /var/lib/apt/lists/* && \
    sh install-composer.sh && \
    docker-php-ext-install -j$(nproc) pdo_mysql && \
    pecl install xdebug && docker-php-ext-enable xdebug && \
    docker-php-ext-install -j$(nproc) opcache && \
    pecl install memcached && docker-php-ext-enable memcached && \
    docker-php-ext-install -j$(nproc) curl