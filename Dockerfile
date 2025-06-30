
FROM node:20 AS node_builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .

RUN mkdir -p vendor/tightenco/ziggy && \
    echo "export const ZiggyVue = { install: () => {} }; export default ZiggyVue;" > vendor/tightenco/ziggy/index.js

RUN npm run build

FROM php:8.3-fpm AS dev

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY . .

COPY --from=node_builder /app/public/build /var/www/html/public/build

RUN php artisan ziggy:generate

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

FROM php:8.3-fpm AS production

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev nginx netcat-openbsd \
    && docker-php-ext-install pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

COPY --from=node_builder /app/public/build /var/www/html/public/build

RUN php artisan ziggy:generate

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

RUN sed -i 's/listen = .*|listen = 0.0.0.0:9000|' /usr/local/etc/php-fpm.d/www.conf && \
    echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

RUN echo '#!/bin/bash\n\
echo "=== Démarrage du conteneur ==="\n\
\n\
# Configuration des permissions\n\
chown -R www-data:www-data /var/www/html\n\
chmod -R 755 /var/www/html/storage\n\
chmod -R 755 /var/www/html/bootstrap/cache\n\
\n\
# Configuration de l'\''environnement\n\
if [ -z "$APP_ENV" ]; then\n\
    export APP_ENV=production\n\
fi\n\
\n\
if [ -z "$APP_DEBUG" ]; then\n\
    export APP_DEBUG=false\n\
fi\n\
\n\
echo "Environnement: $APP_ENV"\n\
echo "Debug: $APP_DEBUG"\n\
\n\
# Génération de la clé d'\''application si elle n'\''existe pas\n\
if [ ! -f /var/www/html/.env ]; then\n\
    echo "Création du fichier .env..."\n\
    cp /var/www/html/.env.example /var/www/html/.env\n\
fi\n\
\n\
# Génération de la clé d'\''application\n\
cd /var/www/html\n\
php artisan key:generate --force\n\
\n\
# Cache des configurations (seulement en production)\n\
if [ "$APP_ENV" = "production" ]; then\n\
    echo "Optimisation des caches..."\n\
    php artisan config:cache --no-interaction || true\n\
    php artisan route:cache --no-interaction || true\n\
    php artisan view:cache --no-interaction || true\n\
fi\n\
\n\
# Démarrage de PHP-FPM\n\
echo "Démarrage de PHP-FPM..."\n\
php-fpm -D\n\
\n\
# Attendre que PHP-FPM soit prêt\n\
echo "Attente de PHP-FPM..."\n\
for i in {1..30}; do\n\
    if pgrep -f "php-fpm" > /dev/null; then\n\
        echo "PHP-FPM démarré avec succès"\n\
        break\n\
    fi\n\
    echo "Attente... ($i/30)"\n\
    sleep 2\n\
done\n\
\n\
# Vérification que PHP-FPM écoute bien\n\
if ! netstat -tlnp | grep :9000 > /dev/null; then\n\
    echo "ERREUR: PHP-FPM n'\''écoute pas sur le port 9000"\n\
    exit 1\n\
fi\n\
\n\
echo "PHP-FPM est prêt et écoute sur le port 9000"\n\
\n\
# Démarrage de Nginx\n\
echo "Démarrage de Nginx..."\n\
exec nginx -g "daemon off;"\n\
' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
