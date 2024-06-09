FROM php:7.3.5-apache-stretch

RUN apt-get update && apt-get install -y libpq-dev libmcrypt-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql mbstring zip bcmath

RUN a2enmod rewrite && service apache2 restart

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

ENV APP_ROOT /app

ENV APACHE_DOCUMENT_ROOT ${APP_ROOT}/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR ${APP_ROOT}

COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --no-ansi \
    --no-interaction \
    --no-plugins \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-dev

COPY . $APP_ROOT

RUN composer run-script --no-dev post-autoload-dump \
    && chown -R www-data:www-data $APP_ROOT/storage $APP_ROOT/bootstrap/cache \
    && rm /var/log/apache2/access.log
