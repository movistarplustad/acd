FROM tadacrottdes.azurecr.io/movistar-base-image:2.0.0-SNAPSHOT as development

# SSL
# TODO: certificado no autofirmado
RUN a2enmod ssl \
    && a2ensite default-ssl \
    && openssl req -subj '/CN=example.com/O=Movistar LTD./C=ES' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem

# Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.4.2

# Install git an unzip (used by composer)
RUN apt-get --allow-releaseinfo-change-suite update \
  && apt-get install -y git unzip

# Production stage
FROM  development AS production

COPY --chown=www-data:www-data . /var/www/html