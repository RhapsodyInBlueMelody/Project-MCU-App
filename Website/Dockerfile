FROM php:8.4-apache-bullseye

# Install system dependencies and PHP extensions required by CodeIgniter 4
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    neovim \
    && docker-php-ext-install mysqli mbstring exif pcntl bcmath gd intl \
    && docker-php-ext-enable mysqli mbstring exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies
RUN composer update --no-dev --optimize-autoloader

# Set proper permissions
# RUN chown -R www-data:www-data /var/www/html \
#    && chmod -R 755 /var/www/html \
#    && chmod -R 777 /var/www/html/writable

#RUN echo '#!/bin/bash\nchown -R www-data:www-data /var/www/html\nchmod -R 755 /var/www/html\nchmod -R 777 /var/www/html/writable\napache2-foreground' > /usr/local/bin/start.sh \
#    && chmod +x /usr/local/bin/start.sh

# Configure Apache
RUN a2enmod rewrite
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80

# Start Apache with proper permissions
CMD ["apache2-foreground"]
