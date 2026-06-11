FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN mkdir -p /var/www/html/05/uploads/bukti \
    && chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]
