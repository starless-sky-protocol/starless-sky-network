## System requeriments

- PHP 7.0 or higher
- Apache, Nginx or Litespeed

## Installing

You will need [composer](https://getcomposer.org/download/) to wrap things up. Firstly, clone this repository and put where your network server is reading root folder.

Starless Sky Network does not use a database to store information. Files and indexing are used to fetch data. The idea of the system is not to search for information.

After cloning the repository to the root directory of your server's domain, install the project's dependencies with composer:

    composer install

After that, let's start configuring the server. Clone the configuration file using:

    cd ./src/system/application/Config
    cp config.php.example config.php

With this, we will have the configuration file that will define configuration constants of the server that is running.

To read the configuration file documentation, see the file [`doc/environment.md`](environment.md)