FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app  # Set the working directory to /app

COPY . /app

RUN wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer
RUN composer install --no-dev

RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh
