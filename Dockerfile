# Stage 1: Build assets with Node.js
FROM node:18-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP with Apache
FROM php:8.2-apache
WORKDIR /var/www/html
# Install PHP extensions and Composer
RUN apt-get update && apt-get install -y git libzip-dev zip unzip && \
    docker-php-ext-install pdo_mysql zip && \
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
EXPOSE 80
CMD ["apache2-foreground"]