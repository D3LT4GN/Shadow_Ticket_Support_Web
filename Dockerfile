# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos las dependencias necesarias para PostgreSQL
# libpq-dev es necesaria para compilar el driver pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitamos el módulo rewrite de Apache (útil para rutas amigables si se requiere)
RUN a2enmod rewrite

# Copiamos todo tu código fuente al directorio público del contenedor
COPY . /var/www/html/

# Exponemos el puerto 80 (interno del contenedor)
EXPOSE 80