# Étape 1 : Builder Node
FROM node:20 AS node_builder

WORKDIR /app

# Copie des fichiers nécessaires au build
COPY package.json package-lock.json ./
RUN npm install

COPY . .

# On a besoin que le dossier "vendor" existe pour Ziggy (installé par Composer)
# On va faker temporairement pour que ça ne plante pas
RUN mkdir -p vendor/tightenco && echo "export default {};" > vendor/tightenco/ziggy.js

RUN npm run build

# Étape 2 : PHP (serveur Laravel)
FROM php:8.3-fpm

# Dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql zip gd

WORKDIR /var/www

# Copie de tout le projet
COPY . .

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copie du build front compilé
COPY --from=node_builder /app/public/build /var/www/public/build

# Droits
RUN chown -R www-data:www-data /var/www