# Build-Basis: PHP mit Apache
FROM php:8.2-apache

# Aktiviere Apache-Module
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel

# System-Pakete und PHP-Erweiterungen installieren
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    zip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libldap2-dev \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    intl \
    zip \
    gd \
    ldap

# Optional: Xdebug installieren
RUN pecl install xdebug && docker-php-ext-enable xdebug

# ✅ Composer von offizieller Composer-Image-Version übernehmen
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#RUN composer install --prefer-dist --no-interaction

## entrypoint.sh ins Image kopieren und ausführbar machen
#COPY api/entrypoint.sh /entrypoint.sh
#RUN chmod +x /entrypoint.sh
#
## Entrypoint definieren
#ENTRYPOINT ["/entrypoint.sh"]
