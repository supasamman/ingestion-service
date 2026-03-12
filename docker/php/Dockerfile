FROM php:8.4-fpm AS php-base

WORKDIR /var/www/app

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    git \
    tzdata \
    curl \
    zip \
    unzip \
 && rm -rf /var/lib/apt/lists/* \
 && mkdir -p /var/www/app \
 && chown -R www-data:www-data /var/www/app

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        intl \
        amqp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/conf/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/conf/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/php/conf/www.conf /usr/local/etc/php-fpm.d/www.conf


# === Development image ===
FROM php-base AS dev

ENV APP_ENV=dev \
    APP_DEBUG=1

USER www-data

CMD ["php-fpm"]
