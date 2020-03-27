FROM php:7.0.33-cli-alpine as RUNNER

COPY --from=composer /usr/bin/composer /usr/bin/composer

#RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#
#RUN apk add pcre-dev ${PHPIZE_DEPS} \
#    && pecl install xdebug \
#    && docker-php-ext-enable xdebug

WORKDIR /var/app/tester

CMD tail -f /dev/null