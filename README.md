# Guestbook
Website guest book written in PHP. It is forked from: [https://github.com/trzemecki/guestbook](https://github.com/trzemecki/guestbook)

There are two versions of this application:
 - The first version on the "master" branch has been updated to use MySQL/MariaDB instead of SQLite. It is also updated to work inside a container image for deployment on the OpenShift or Kubernetes platform.
 - The second version on the "mssql" branch has been updated to use Microsoft SQL server and run in Windows IIS or within a container. If you wish to use the Microsoft SQL server version, be sure to clone this repo and then `git checkout mssql`

**NOTE:** This is EXAMPLE CODE. I would not use this in production if I was you.


## License
Library is licensed under BSD 3-Clause License

## Requirements

This code has been tested using **PHP 7.4** with **pdo_mysql** extension

## Configuration

Create environment variables for the following:

REMOTE_DSN='mysql:dbname=yourdbname;host=127.0.0.1'
REMOTE_DB_USER='username'
REMOTE_DB_PASS='password'

## Running from a Container

```
podman build -t guestbook-php:latest .
```

```
podman run -d --replace --name=mysql -p 3306:3306 \
      -v "$(pwd)/mysql:/var/lib/mysql:z" \
      -e MYSQL_USER="username" \
      -e MYSQL_PASSWORD="password" \
      -e MYSQL_DATABASE="guestbook" \
      centos/mariadb-102-centos7
```

```
podman run -p 8080:80 --name=guestbook-php -e REMOTE_DSN='mysql:dbname=guestbook;host=<yourIPHere>' \
      -e REMOTE_DB_USER="username" \
      -e REMOTE_DB_PASS="password" guestbook-php:latest
```

## Installation info
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
