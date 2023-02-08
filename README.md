# Guestbook
Website guest book written in PHP. It is forked from: [https://github.com/trzemecki/guestbook](https://github.com/trzemecki/guestbook). The main branch of this repo is designed to work with PHP and MySQL/MariaDB. This is a branch that has been written to be hosted from a Windows Server with IIS, and PHP 8.0 Installed. It uses Microsoft SQL Server (Express) as the database back end. 

**NOTE:** This is *EXAMPLE CODE*. I would not use this in production if I was you.

The instructions and code in this repo were tested on a Windows 2019 Server running IIS 10, and [SQL Server Express 2019](https://www.microsoft.com/en-us/download/details.aspx?id=101064)

## License
Library is licensed under BSD 3-Clause License

## Requirements

### IIS Configuration

You will need to have a working IIS Server with PHP8.0 installed to host this code in Windows. Configuration of IIS/PHP8.0 is outside the scope of this document. See the following articles for general guidance on getting this setup:

* [Install and Configure PHP](https://learn.microsoft.com/en-us/iis/application-frameworks/install-and-configure-php-on-iis/install-and-configure-php)
      * Be sure to use the 8.0.x version of PHP
* [PHP Extensions](https://windows.php.net/downloads/pecl/releases/)
      * You will need to manually download and install the php_pdo_sqlsrv and php_xmlrpc extensions and install them in the `ext` directory of you PHP install
      * You will need to update the php.ini to load these additional extensions


Once you have properly configured IIS to use PHP 8.0, copy the contents of the src directory to `C:\guestbook-php` and then configure a new site in IIS to point to this directory.

### SQL Server Express

You will need to install SQL Server Express 2019, and enable "Mixed Mode" authentication when asked.
You will then need to enable TCP/IP connections to SQL Express following the directions here:
[Configure SQL Server Express to allow Remote TCP/IP connections](https://www.teamdotnetnuke.com/en-us/article/445-configure-sql-server-express-to-allow-remote-tcp-ip-connections-on-port-1433)

You will also need to do the following:
* create a blank database called "guestbook"
* create a SQL username/password for use

## Configuration

The app has been updated to put the database configuration into a separate file that can be overridden later as a kubernetes secret. You will need to edit the file `config\database-config.php` to point to your SQL Server instance. If you are running this application in Windows, the file should look like the following:

```php
<?php
    # Get database settings from environment variables
    $dsn = "sqlsrv:server=WIN2019IIS.EXAMPLE.NET, 1433\SQLEXPRESS;database=guestbook";
    $user = "guestbookusr";
    $password = "dbpass2023!";
?>
```

where "WIN2019IIS.EXAMPLE.NET" points to your fully qualified DNS name for the server running MS SQL.

## Running the Guestbook app and Microsoft SQL Server in OpenShift

See the [Readme](kubernetes/ocp/Readme.md) file for instructions on deploying the application in OpenShift

## Running the Guestbook app locally from a Container

This app can also be run from a Linux container. You will need to build the container first before running it. The instructions below assume you are using `podman` but you should be able to replace all instances of `podman` with `docker` and be able to complete these steps.

### Build the container image

Start by building the image:

```
$ podman build -t guestbook-php:latest .
```

### Running MS SQL locally from a container

```
$ podman run -d -e "ACCEPT_EULA=Y" -e "MSSQL_SA_PASSWORD=GuestBookpass1!" \
   -p 1433:1433 --name sql1 --hostname sql1 \
   mcr.microsoft.com/mssql/server:2019-latest
```

Create guestbook database.

```
$ podman exec -it sql1 "bash"
mssql@sql1:/$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "CREATE DATABASE guestbook"
mssql@sql1:/$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "SELECT Name from sys.databases"
Name                                                                                                                            
--------------------------------------------------------------------------------------------------------------------------------
master                     
tempdb
model
msdb
guestbook                                                                                                                       
(5 rows affected)
# exit
```

## Running the Application
After unzip files on your server go to ~/admin/ and try login with 

Name: **admin**  
Password: **admin**

Then go to settings and change user name and password.

## Features
- adding new entries to considered by the admin
- approving/discarding added entries
- removing approved entires
- store removed entries in bin

## Screen captures

### Adding entry site

![Entry site image](docs/image_1.png)

![Entry site image with input data](docs/image_2.png)

### Admin approving site

![Approving site image](docs/image_3.png)

### Entry review site

![Review site image](docs/image_4.png)
