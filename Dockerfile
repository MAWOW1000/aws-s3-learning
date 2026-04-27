FROM php:8.4-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Expose port (Render sets PORT env var)
EXPOSE 8000

# Generate .env at runtime from env vars, then start Laravel
CMD sh -c 'echo "APP_KEY=$APP_KEY" > /app/.env && echo "APP_ENV=$APP_ENV" >> /app/.env && echo "APP_DEBUG=$APP_DEBUG" >> /app/.env && echo "AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID" >> /app/.env && echo "AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY" >> /app/.env && echo "AWS_DEFAULT_REGION=$AWS_DEFAULT_REGION" >> /app/.env && echo "AWS_BUCKET=$AWS_BUCKET" >> /app/.env && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}'
