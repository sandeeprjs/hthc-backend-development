FROM php:8.1-fpm-alpine

# Install dependencies
RUN apk add --no-cache nginx wget gettext

# Create necessary directories
RUN mkdir -p /run/nginx

# Set working directory
WORKDIR /app

# Copy application code
COPY . /app

# Install Composer
RUN wget http://getcomposer.org/composer.phar \
    && chmod a+x composer.phar \
    && mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
RUN composer install --no-dev

# Set permissions
RUN chown -R www-data:www-data /app

# Copy Nginx configuration template
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template

# Expose the port (optional, for documentation purposes)
EXPOSE 8080

# Start the application
CMD ["sh", "/app/docker/startup.sh"]
