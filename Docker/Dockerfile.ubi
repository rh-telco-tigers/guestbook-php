FROM registry.access.redhat.com/ubi8/php-80 AS builder

USER 0

# Install MS Drivers
RUN curl https://packages.microsoft.com/config/rhel/8/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum install -y msodbcsql18 libtool-ltdl unixODBC-devel php-devel php-pear

# Fix issue with linker
RUN ln -s  libltdl.so.7.3.1 /usr/lib64/libltdl.so

# Compile and install MSSQL PHP PDO Driver
RUN pecl channel-update pecl.php.net
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv

FROM registry.access.redhat.com/ubi8/php-80

USER 0

# Install MS Drivers
RUN curl https://packages.microsoft.com/config/rhel/8/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN ACCEPT_EULA=Y yum install -y msodbcsql18 libtool-ltdl php-pear

# Fix issue with linker
RUN ln -s  libltdl.so.7.3.1 /usr/lib64/libltdl.so

COPY --from=builder /usr/lib64/php/modules/pdo_sqlsrv.so /usr/lib64/php/modules/pdo_sqlsrv.so
COPY --from=builder /usr/lib64/php/modules/sqlsrv.so /usr/lib64/php/modules/sqlsrv.so

COPY 30-sqlsrv.ini 30-pdo_sqlsrv.ini /etc/php.d/

USER 1001
# ADD src /var/www/html
ADD src /opt/app-root/src

EXPOSE 8080

#CMD php-fpm & httpd -D FOREGROUND
CMD /usr/libexec/s2i/run 