# Use the official PHP image as the base
FROM php:8.2-fpm

# Install system dependencies and PHP extensions required for Symfony
RUN apt-get update && apt-get install -y git libicu-dev zip unzip supervisor

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory in the container
WORKDIR /var/www/app

# Copy the Symfony application files to the container
COPY . /var/www/app

# Install PHP dependencies
RUN composer install

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy Supervisor configuration
COPY messenger_workers.conf /etc/supervisor/conf.d/messenger_workers.conf

# Expose the necessary port to the outside world
EXPOSE 9000

ENTRYPOINT ["entrypoint.sh", "db_cont:3306", "-t", "60"]