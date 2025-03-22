FROM php:apache
RUN apt-get update && apt-get install -y libpq-dev libxslt-dev
RUN docker-php-ext-install pdo pdo_pgsql xsl
RUN a2enmod rewrite
