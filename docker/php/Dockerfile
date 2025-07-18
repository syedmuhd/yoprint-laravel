FROM php:8.4-fpm

ARG UID
ARG GID

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev libpng-dev \
    gnupg2 ca-certificates lsb-release software-properties-common \
    nodejs npm \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl gd \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install latest Node.js (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# Copy custom PHP config
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Copy custom entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/custom-entrypoint.sh"]

RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

CMD ["php-fpm"]

WORKDIR /var/www/html
