#!/bin/sh
set -e

echo "Waiting for MySQL..."
while ! php artisan migrate --force; do
  echo "MySQL is not ready yet, retrying..."
  sleep 5
done

echo "Running optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# exec "$@"
