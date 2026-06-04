FROM php:8.2-cli

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio
WORKDIR /app

# Copiar archivos
COPY . .

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias frontend
RUN npm install && npm run build

# Permisos
RUN chmod -R 777 storage bootstrap/cache

# Puerto Render
EXPOSE 10000

# Comando inicio
CMD php artisan serve --host=0.0.0.0 --port=10000