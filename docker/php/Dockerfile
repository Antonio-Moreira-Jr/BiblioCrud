FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
git \
unzip \
libzip-dev \
&& docker-php-ext-install pdo pdo_mysql zip \
&& pecl install redis \
&& docker-php-ext-enable redis \
&& rm -rf /var/lib/apt/lists/*

RUN echo "session.save_handler = redis" > /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.save_path = \"tcp://redis:6379\"" >> /usr/local/etc/php/conf.d/redis-session.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html