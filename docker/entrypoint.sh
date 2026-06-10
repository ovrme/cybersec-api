#!/bin/bash
set -e

# Render routes traffic to $PORT — make Apache listen there
sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force

exec apache2-foreground
