FROM php:8.2-apache
# Enable Apache modules
RUN a2enmod rewrite
# Install any extensions you need
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN pecl install redis && docker-php-ext-enable redis
# Set the working directory to /var/www/html
WORKDIR /var/www/html
# Copy the source code in /php into the container at /var/www/html
COPY ./php .
