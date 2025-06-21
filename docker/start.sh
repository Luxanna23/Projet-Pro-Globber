#!/bin/bash

echo "=== Démarrage du conteneur ==="

# Vérification des permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "Démarrage de PHP-FPM..."
php-fpm -D

# Attendre plus longtemps que PHP-FPM soit vraiment prêt
echo "Attente de PHP-FPM..."
sleep 5

# NE PAS faire les caches au démarrage (ça peut causer des timeouts)
# cd /var/www/html
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "Démarrage de Nginx..."
exec nginx -g "daemon off;"