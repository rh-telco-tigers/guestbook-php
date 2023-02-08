# Docker Files

This directory contains two different Dockerfiles. 

## Dockerfile.php

This file is a Dockerfile that is built on the official php dockerfile. This Dockerfile (and image) is based on 

## Dockerfile.ubi

This file uses the Red Hat UBI8-php8 image as a baseline. This means that it is using the Red Hat binaries as a base for building the image.

## Adding the Microsoft SQL Server php libraries

For both the UBI and official PHP image, we need to add the Microsoft SQL Server library and pdo_sqlsrv library to make this application work.
The commands in the dockerfiles are based on the install instructions from [Linux and macOS Installation Tutorial for the Microsoft Drivers for PHP for SQL Server](https://learn.microsoft.com/en-us/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-ver16#installing-on-debian)