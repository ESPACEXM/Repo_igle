# Usar la imagen oficial de PHP 8.2 con Apache
FROM php:8.2-apache

# Establecer variables de entorno para la URL de la aplicación durante el build
ARG APP_URL=https://contabilidad-sl9d.onrender.com
ENV APP_URL=$APP_URL
ENV NODE_ENV=production

# Instalar dependencias del sistema incluyendo Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Configurar Apache
RUN a2enmod rewrite

# Cambiar el DocumentRoot de Apache a /var/www/html/public
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Verificar que existen los archivos necesarios antes de construir
RUN echo "Checking for required files..." && \
    ls -la resources/css/app.css resources/js/app.js package.json vite.config.js || (echo "Some required files missing" && exit 1)

# Instalar dependencias de Node.js y construir assets con logging detallado
RUN echo "Installing Node.js dependencies..." && \
    npm install && \
    echo "Node.js dependencies installed. Checking vite version..." && \
    npx vite --version && \
    echo "Building Vite assets..." && \
    # Run the build and capture both stdout and stderr to see any errors
    npx vite build --mode production || (echo "Vite build failed!" && exit 1) && \
    echo "Vite build completed. Checking manifest..." && \
    ls -la public/build/ || (echo "Manifest directory not found!" && exit 1) && \
    if [ -f public/build/manifest.json ]; then \
        echo "Manifest found! Contents:" && \
        cat public/build/manifest.json; \
    else \
        echo "Manifest NOT found!"; \
        ls -la public/build/; \
        exit 1; \
    fi

# Limpiar caché de Composer
RUN composer clear-cache

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 755 /var/www/html/public

# Exponer puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]