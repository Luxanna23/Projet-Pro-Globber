# Étape 1 : Builder Node
FROM node:20 AS node_builder
WORKDIR /app

# Copie des fichiers nécessaires au build
COPY package.json package-lock.json ./
RUN npm install

# Copie du code source
COPY . .

# Création d'un fichier Ziggy temporaire avec les exports nécessaires
RUN mkdir -p vendor/tightenco/ziggy && \
    echo "export const ZiggyVue = { install: () => {} }; export default ZiggyVue;" > vendor/tightenco/ziggy/index.js

# Build du front
RUN npm run build

# Étape 2 : PHP + Nginx (serveur Laravel)
FROM php:8.3-fpm AS production

# Dépendances système + nginx
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev nginx supervisor \
    && docker-php-ext-install pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du composer.json/lock d'abord pour optimiser le cache
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# Copie de tout le projet
COPY . .

# Réinstallation avec les scripts maintenant que tout est copié
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Copie du build front compilé depuis l'étape Node
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Génération du vrai fichier Ziggy maintenant que Laravel est installé
RUN php artisan ziggy:generate

# Configuration nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Configuration supervisor pour gérer nginx + php-fpm
RUN mkdir -p /var/log/supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Railway utilise le port 80 par défaut
EXPOSE 80

# Lancement de supervisor qui gère nginx + php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]