FROM php:8.2-fpm-alpine

WORKDIR /app

# Assuming composer.json and composer.lock are in the same directory as Dockerfile
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

COPY . .

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
