# Usa una imagen base de PHP con Apache
FROM php:8.2-apache

# Instala las extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos del proyecto
COPY . .


# Instala las dependencias de Laravel
RUN composer install

# Expone el puerto en el que se ejecutará la aplicación
EXPOSE 8000

# Comando para iniciar el servidor de desarrollo
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]