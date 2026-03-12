# Stage 1: Build assets with Node.js
FROM node:20-alpine AS builder
WORKDIR /app
ARG APP_URL=https://contabilidad-sl9d.onrender.com
ENV APP_URL=$APP_URL
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP with Laravel
FROM php:8.2-cli
WORKDIR /var/www/html

# Install PHP extensions and Composer
RUN apt-get update && apt-get install -y git libzip-dev zip unzip libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql && \
    docker-php-ext-install pdo_pgsql pgsql zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application (without node_modules) to avoid copying large files
COPY . .
RUN rm -rf node_modules

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the built assets from the builder stage
COPY --from=builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port Render will use
EXPOSE 10000

# Run migrations and start the Laravel server - NO SEEDING to avoid timeout
CMD ["sh", "-c", "php artisan migrate --force --no-interaction --quiet && \
    echo 'Laravel server starting on port 10000...' && \
    php artisan serve --host=0.0.0.0 --port=10000"]
