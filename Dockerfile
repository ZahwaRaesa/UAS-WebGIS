FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN mkdir -p /var/www/html/05/uploads/bukti \
    && chown -R www-data:www-data /var/www/html \
    && sed -i 's/\r$//' /var/www/html/start.sh \
    && chmod +x /var/www/html/start.sh

CMD ["/var/www/html/start.sh"]
