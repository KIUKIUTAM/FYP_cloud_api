FROM php:fpm
# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip

# Enable extensions (optional, as docker-php-ext-install usually enables them)
RUN docker-php-ext-enable pdo_mysql mysqli

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN if [ -f composer.json ]; then \
    composer install --no-dev --no-interaction --optimize-autoloader; \
    fi