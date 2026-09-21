FROM php:8.2-apache
RUN apt-get update && apt-get install -y libicu-dev libpq-dev unzip git \
 && docker-php-ext-install intl mysqli pgsql pdo_pgsql \
 && a2enmod rewrite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
 /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
 && sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader \
 && chown -R www-data:www-data writable
CMD ["sh","-c","cp /etc/secrets/.env .env; php spark migrate; apache2-foreground"]
