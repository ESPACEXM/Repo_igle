# Stage 1: Build assets with Node.js
FROM node:20-alpine AS builder
WORKDIR /app
# Set the APP_URL for Vite base configuration
ARG APP_URL=https://contabilidad-sl9d.onrender.com
ENV APP_URL=$APP_URL
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP with Apache
FROM php:8.2-apache
WORKDIR /var/www/html
# Install PHP extensions and Composer
RUN apt-get update && apt-get install -y git libzip-dev zip unzip libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql && \
    docker-php-ext-install pdo_pgsql pgsql zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Copy the application (without node_modules) to avoid copying large files
COPY . .
# Remove the node_modules that we don't need in the PHP stage (if any)
RUN rm -rf node_modules
# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader
# Copy the built assets from the builder stage
COPY --from=builder /app/public/build ./public/build
# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
# Configure Apache
RUN a2enmod rewrite
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
EXPOSE $PORT
# Run migrations and start Apache
CMD ["sh", "-c", "sed -i -e \"s/Listen 80/Listen $PORT/\" /etc/apache2/ports.conf && php artisan migrate --force --verbose && php artisan db:seed --force --verbose && apache2-foreground"]