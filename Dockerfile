FROM php:8.4-cli

# Extensions système
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    zip unzip git curl nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier les fichiers de dépendances d'abord (cache Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY package.json package-lock.json ./
RUN npm install

# Copier tout le projet
COPY . .

# Env pour le build
RUN cp .env.example .env && php artisan key:generate --force

# Build frontend + cache Laravel
RUN npm run build \
    && php artisan package:discover --ansi \
    && php artisan view:cache

# Permissions storage
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
