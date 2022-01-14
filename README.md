# api_mspr
[Slim framework 4](https://www.slimframework.com/)

## Installation and Execution
To do or to check before launching the application:
* [PHP 7.2](https://www.php.net/) or newer and [Composer](https://getcomposer.org/download/) are installed,
* Point virtual host to `public/` directory,
* Install pdo_pgsql extension for PHP (for postgresql database).

Then go to the application directory and do the following command:
```shell
composer update
```

After the installation, start the execution with:
```shell
composer start
```

## Tests
To test the functions of the application, do the following command:
```shell
composer test
```
Test coverage is not available due to the test method used.

If the error message `Error: No code coverage driver is available` appear, install Xdebug package for PHP:
```shell
apt install php-xdebug
```