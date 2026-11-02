FROM php:8.3.36-apache-bookworm

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite headers expires

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-studios.ini
