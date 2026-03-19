FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_mysql zip bcmath sockets \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy only composer.json first for layer caching
COPY composer.json ./

# Install dependencies - verbose so errors are visible
RUN composer install \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --verbose

# Copy the rest of the application
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
