FROM php:8.0-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev bash nodejs npm git autoconf build-base pcre-dev
RUN docker-php-ext-install pdo pdo_pgsql

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

RUN if [[ -z "$INSTALL_XDEBUG" ]] ; then pecl install xdebug && docker-php-ext-enable xdebug ; fi

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN ln -s /usr/local/bin/php /usr/local/bin/php8.0
RUN ln -sf /dev/stderr /tmp/php8.0-fpm.log

RUN wget https://raw.githubusercontent.com/eficode/wait-for/master/wait-for -O /wait-for && chmod +x /wait-for

COPY ./docker/php/app-entrypoint.sh /app-entrypoint.sh

RUN chmod +x /app-entrypoint.sh

COPY . /app
WORKDIR /app
RUN composer install && rm -rf ~/.composer/cache/
RUN npm i

ENTRYPOINT ["/app-entrypoint.sh"]
CMD ["php-fpm", "-F"]
