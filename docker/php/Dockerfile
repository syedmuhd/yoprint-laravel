FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev libpng-dev \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl gd

# Copy custom config
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Copy custom entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/custom-entrypoint.sh"]

# Default command (must forward to php-fpm)
CMD ["php-fpm"]

WORKDIR /var/www/html
