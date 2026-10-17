FROM php:8.4-apache

# PHP extensions required by the application
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql zip intl gd exif bcmath opcache

# Apache: enable rewrite (Laravel's public/.htaccess) and point the
# document root to Laravel's public/ directory
RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
