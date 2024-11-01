# Use a PHP 8.2 FPM image with Alpine Linux
FROM php:8.2-fpm-alpine

# Set the working directory
WORKDIR /app

# Copy the composer.json and composer.lock files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Expose the port
EXPOSE 8080

# Start the development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
