FROM php:5.6.40-apache

RUN docker-php-ext-install mysql mysqli pdo_mysql mbstring

RUN a2enmod rewrite headers expires

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-studios.ini
