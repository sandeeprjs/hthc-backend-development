# Use PHP 8.2 with FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    nginx \
    libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions, including gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy all application files to the working directory
COPY . /var/www/html

# Set permissions for /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Copy Nginx configuration file
COPY nginx.conf /etc/nginx/nginx.conf

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Generate application key
RUN php artisan key:generate

# Expose port 8080
EXPOSE 8080

# Start Nginx and PHP-FPM
CMD php-fpm -D && nginx -g "daemon off;"
