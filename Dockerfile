FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT=/var/www/html/public
ENV APP_ENV=production
ENV APP_DEBUG=false

WORKDIR /var/www/html

COPY . .

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Install Node.js 22 and build Vite assets
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && npm cache clean --force \
    && rm -rf /var/lib/apt/lists/*

# Laravel permissions
RUN chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
    && chmod -R 775 \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache

CMD ["/start.sh"]
