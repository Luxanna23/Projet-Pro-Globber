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

# Étape 2 : PHP (serveur Laravel)
FROM php:8.3-fpm AS dev

# Dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev \
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

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]