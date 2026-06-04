FROM php:8.4-cli

# Dependencias sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    nodejs \
    npm

# Configurar GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    gd \
    zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio
WORKDIR /app

# Copiar proyecto
COPY . .

# Instalar Laravel
RUN composer install --no-dev --optimize-autoloader

# Compilar frontend
RUN npm install && npm run build

# Permisos
RUN chmod -R 777 storage bootstrap/cache

# Puerto Render
EXPOSE 10000

# Ejecutar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000