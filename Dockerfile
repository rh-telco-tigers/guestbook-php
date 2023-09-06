FROM php:7.4-apache

RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable mysqli pdo_mysql
RUN apt-get update && apt-get upgrade -y

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY start-apache.sh /usr/local/bin
RUN a2enmod rewrite

# Copy application source
COPY src /var/www/public
RUN chown -R www-data:www-data /var/www

RUN mkdir /tmp/sessions
#setup directory permissions for running in OCP
RUN chown -R www-data:0 /tmp/sessions && \
   chmod -R g=u /tmp/sessions && \
   chgrp -R 0 /etc/apache2 && \
   chmod -R g=u /etc/apache2 && \
   chgrp -R 0 /var/log/apache2 && \
   chmod -R g=u /var/log/apache2
CMD ["start-apache.sh"]