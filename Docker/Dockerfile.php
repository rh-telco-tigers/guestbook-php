FROM php:8.0-apache-buster
# Need to use buster version, there is an issue building sqlsrv with more recent bullseye builds

ENV ACCEPT_EULA=Y

# Install prerequisites required for tools and extensions installed later on.
RUN apt-get update \
    && apt-get install -y apt-transport-https gnupg2 libpng-dev libzip-dev nano unzip locales \
    && rm -rf /var/lib/apt/lists/*

# Configure Locales
RUN sed -i 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/g' /etc/locale.gen \
    && locale-gen

# Install prerequisites for the sqlsrv and pdo_sqlsrv PHP extensions.
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y msodbcsql17 mssql-tools unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install sqlsrv \
    && pecl install pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY start-apache.sh /usr/local/bin
RUN a2enmod rewrite

# Copy application source
COPY src /var/www/public
RUN chown -R www-data:www-data /var/www

RUN mkdir /tmp/sessions
RUN chown -R www-data:www-data /tmp/sessions

CMD ["start-apache.sh"]