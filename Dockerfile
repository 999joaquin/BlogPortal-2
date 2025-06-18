FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache to listen on Railway's PORT
RUN sed -i 's/Listen 80/Listen ${PORT:-80}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT:-80}/' /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create a simple startup script
RUN echo '#!/bin/bash\n\
echo "Starting Apache on port ${PORT:-80}"\n\
echo "Environment variables:"\n\
env | grep -E "(MYSQL|PORT)" || echo "No MySQL env vars found"\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose the port
EXPOSE ${PORT:-80}

# Remove health check for now to avoid timeout issues
# HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
#   CMD curl -f http://localhost:${PORT:-80}/ || exit 1

# Start Apache
CMD ["/start.sh"]
