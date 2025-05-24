FROM php:8.3-fpm

# passer apt en non‐interactif et forcer l’usage des conf défault
ARG DEBIAN_FRONTEND=noninteractive
ENV DEBIAN_FRONTEND=${DEBIAN_FRONTEND}

# Install system dependencies en non‐interactive
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      unzip \
      zip \
      libpng-dev \
      libonig-dev \
      libxml2-dev \
      libzip-dev \
      curl \
      tzdata \
    -o Dpkg::Options::="--force-confdef" \
    -o Dpkg::Options::="--force-confold" && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Installer les dépendances PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Installer Node.js via un multi‐stage basé sur l’image officielle Node
FROM node:22-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install

# Revenir à l’image PHP et copier les modules node compilés
FROM php:8.3-fpm
COPY --from=node_builder /app/node_modules /var/www/html/node_modules

WORKDIR /var/www/html

# Copier le code applicatif
COPY . .

# Ajuster droits
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Exposer et démarrer
EXPOSE 9000
CMD ["php-fpm"]
