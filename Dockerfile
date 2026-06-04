FROM php:8.4-apache

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

# Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    gd \
    zip

# Activar mod_rewrite
RUN a2enmod rewrite

# Configurar Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio
WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# ── CRÍTICO: APP_ENV=production hace que @vite() lea el manifest compilado.
#    Sin esto, @vite() intenta conectarse al dev server (puerto 5173)
#    que no existe en Render → el CSS del login nunca carga.
#    Los dashboards no se ven afectados porque usan Tailwind CDN directamente.
ENV APP_ENV=production

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Build frontend — genera public/build/.vite/manifest.json
RUN npm install
RUN npm run build

# Verificar que el manifest existe y contiene login.css
# Si falla aquí, el deploy falla con mensaje claro en lugar de silenciosamente
RUN test -f public/build/.vite/manifest.json \
    && echo "✓ Vite manifest OK" \
    || (echo "✗ ERROR: public/build/.vite/manifest.json no fue generado" && exit 1)

RUN grep -q "auth/login" public/build/.vite/manifest.json \
    && echo "✓ login.css incluido en manifest" \
    || (echo "✗ ERROR: login.css no está en manifest — verificar vite.config.js input[]" && exit 1)

# Limpiar cachés viejas (por si el COPY trajo caches de desarrollo local)
# NO se ejecuta config:cache aquí porque APP_KEY y DATABASE_URL
# son inyectados por Render en runtime, no en build time.
RUN php artisan config:clear \
    && php artisan view:clear \
    && php artisan route:clear

# Permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache

# Puerto Render
EXPOSE 10000

# Apache puerto 10000
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
