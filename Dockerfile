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

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache for Railway's dynamic port
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

# Create startup script that handles Railway's PORT variable
RUN echo '#!/bin/bash\n\
set -e\n\
echo "=== Railway Startup Script ==="\n\
echo "PORT: ${PORT}"\n\
echo "Environment check:"\n\
env | grep -E "(MYSQL|PORT)" | head -10 || echo "No MySQL/PORT env vars found"\n\
\n\
# Replace PORT placeholder in Apache config\n\
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/ports.conf\n\
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
echo "Apache will listen on port: ${PORT}"\n\
echo "Starting Apache..."\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose the port (Railway will override this)
EXPOSE 80

# Use the startup script
CMD ["/start.sh"]
