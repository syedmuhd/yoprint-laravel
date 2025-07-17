#!/bin/bash

set -e

# Ensure correct permissions (including SQLite)
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache
mkdir -p database

# touch database/database.sqlite

chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache
# chmod 777 database/database.sqlite

# Install composer deps if needed
if [ ! -d "vendor" ]; then
  composer install
fi

if [ ! -d "node_modules" ]; then
  npm install
fi

# Run migrations if database is writable
# php artisan config:clear
# php artisan migrate --force || true
# php artisan horizon &

exec docker-php-entrypoint "$@"
