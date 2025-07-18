#!/bin/bash

set -e

mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Install composer deps if needed
if [ ! -d "vendor" ]; then
  composer install
fi

if [ ! -d "node_modules" ]; then
  npm install && npm run build
fi

exec docker-php-entrypoint "$@"
