FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT=/var/www/html/public
ENV APP_ENV=production
ENV APP_DEBUG=false
# [+] Izinkan skrip startup bawaan image berjalan
ENV RUN_SCRIPTS=1

WORKDIR /var/www/html

COPY . .

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Install Node.js 22 & Build Vite Assets
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules \
    && rm -rf /var/lib/apt/lists/*

# [+] Skrip automatisasi startup (SQLite, Migration, & Cache)
RUN mkdir -p /var/www/html/scripts && \
    echo '#!/bin/sh' > /var/www/html/scripts/entrypoint.sh && \
    echo 'mkdir -p /var/www/html/storage/database' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'touch /var/www/html/storage/database/database.sqlite' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'php artisan config:cache' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'php artisan route:cache' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'php artisan view:cache' >> /var/www/html/scripts/entrypoint.sh && \
    echo 'php artisan migrate --force' >> /var/www/html/scripts/entrypoint.sh && \
    chmod +x /var/www/html/scripts/entrypoint.sh

# Permissions
RUN chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
    && chmod -R 775 \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache

CMD ["/start.sh"]