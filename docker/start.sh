#!/bin/bash

# Configuration du port dynamique pour nginx
if [ -n "$PORT" ]; then
    sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/sites-available/default
    echo "Nginx configured to listen on port $PORT"
else
    echo "Warning: PORT environment variable not set, using default port 80"
fi

# Optimisation Laravel
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting PHP-FPM..."
# Démarrage de PHP-FPM en arrière-plan
php-fpm8.3 --daemonize

echo "Starting Nginx..."
# Démarrage de Nginx en avant-plan (pour que le container reste actif)
exec nginx -g "daemon off;"