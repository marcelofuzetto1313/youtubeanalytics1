FROM php:8.3-cli

# Dependências para composer / zip
RUN apt-get update && apt-get install -y git unzip libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Instala dependências primeiro (cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia o resto
COPY . .

# Garante pastas necessárias
RUN mkdir -p storage && chmod -R 777 storage

# Sobe servidor embutido no PORT do Railway
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]
