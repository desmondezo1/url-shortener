FROM php:8.4-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app/

# Install dependencies
RUN composer install --no-interaction

# Permissions
RUN chmod -R 777 storage

# Expose port 8000
EXPOSE 8000

# Start PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]