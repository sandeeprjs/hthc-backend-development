FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget git \
    && docker-php-ext-install pdo pdo_mysql opcache

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

COPY . /app

# Download Composer
RUN wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer

# Run Composer with verbose logging
RUN composer install --no-dev --verbose

RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh
