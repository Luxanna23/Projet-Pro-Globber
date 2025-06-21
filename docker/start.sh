bash#!/bin/bash

echo "=== Démarrage du conteneur ==="

# Vérification des permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

# Optimisation Laravel
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Démarrage de PHP-FPM..."
php-fpm -D

echo "Attente de PHP-FPM..."
sleep 3

echo "Démarrage de Nginx..."
exec nginx -g "daemon off;"