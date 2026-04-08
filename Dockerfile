FROM php:8.4-cli

# Extensions système + Redis
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev libmagickwand-dev \
    zip unzip git curl nodejs npm supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets zip intl \
    && pecl install redis imagick && docker-php-ext-enable redis imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier les fichiers de dépendances d'abord (cache Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY package.json package-lock.json ./
RUN npm ci --production=false

# Copier tout le projet
COPY . .

# Env pour le build uniquement
RUN cp .env.example .env \
    && rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
    && php artisan package:discover --ansi \
    && php artisan key:generate --force \
    && rm -f .env

# Build frontend
RUN npm run build

# Supprimer les devDependencies node après le build
RUN rm -rf node_modules

# Cache views seulement (config et routes au runtime avec les vraies variables)
RUN php artisan view:cache

# OPcache pour la performance en production
RUN echo "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\nopcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache-prod.ini

# PHP production settings
RUN echo "memory_limit=256M\npost_max_size=64M\nupload_max_filesize=64M\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On" > /usr/local/etc/php/conf.d/production.ini

# Permissions storage
RUN chmod -R 775 storage bootstrap/cache

# Supervisor config pour le queue worker + scheduler
COPY docker/prod/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

# Railway injecte $PORT — le CMD utilise les vraies variables d'env au runtime
CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan route:cache && php artisan event:cache && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
