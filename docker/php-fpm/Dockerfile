FROM php:fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.7.0 \
    && docker-php-ext-enable xdebug

RUN apk add libressl-dev

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY php.ini $PHP_INI_DIR

CMD ["php-fpm"]

EXPOSE 9000

WORKDIR /var/www
