<div id="top"></div>
<br />

<!-- PROJECT LOGO -->
<div align="center">
<h3 align="center">Cerealis API</h3>

  <p align="center">
    Enterprise Resource Management API.
    <br />
    <a href="https://editor.swagger.io/"><strong>Explore the docs</strong></a> with <a href="https://github.com/Inge-Info/MSPR_API/blob/main/swagger.yaml"><strong>Swagger</strong></a>
  </p>
</div>
<br />


<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites-linux">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li>
      <a href="#usage">Usage</a>
      <ul>
        <li><a href="#endpoints">Endpoints</a></li>
        <li><a href="#unit-testing">Unit testing</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->

## About The Project

REST API written in PHP allowing to manage the whole information system related to the management of sessions, admins,
users, and emails. It offers a complete HTTP interface to manage the database and its information secured by an OAuth2
authentication and a permissions' system.

### Built With

* [Slim Framework 4](https://www.slimframework.com/)
    * [OAuth2](https://github.com/bshaffer/oauth2-server-php)
    * [PHPMailer](https://github.com/PHPMailer/PHPMailer)
    * [PHPUnit](https://phpunit.de/)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

### Prerequisites (Linux)

* [PHP](https://www.php.net/) 8.1 with `php-xdebug`
  ```shell
    apt install php8.1 php-xdebug
  ```

* [Composer](https://getcomposer.org/)
  ```shell
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  
  # Global install
  sudo mv composer.phar /usr/local/bin/composer
  ```

### Installation

1. Clone the repo
   ```shell
   git clone https://github.com/Inge-Info/MSPR_API
   ```

2. Install Composer packages
   ```shell
   # With local install
   php composer.phar install
   
   # With global install
   composer install
   ```

3. Create and edit `config.ini`
   ```ini
   [debug] # Display PHP errors
   displayPHPErrors    = "false"
   displayErrorDetails = "false"
   logError            = "false"
   logErrorDetails     = "false"

   [database] # Database access information
   driver    = "mysql"
   host      = "localhost"
   port      = "3306"
   database  = "example_database"
   username  = "example_user"
   password  = "example_password"
   charset   = "utf8mb4"
   collation = "utf8mb14_unicode_ci"
   
   [stmp] # Mail server for newsletter
   host     = "stmp.example.com"
   port     = "1234"
   username = "username"
   password = "password"
   from     = "newsletter@example.com"

   [oauth2] # OAuth2 session configuration
   client_table                      = "clients"
   user_table                        = "admins"
   access_token_table                = "tokens"
   refresh_token_table               = "refresh_tokens"
   scope_table                       = "scopes"
   
   store_encrypted_token_string      = "true"
   access_lifetime                   = "3600"
   token_bearer_header_name          = "Bearer"
   allow_implicit                    = "false"
   allow_credentials_in_request_body = "true"
   always_issue_new_refresh_token    = "false"
   unset_refresh_token_after_use     = "true"
   refresh_token_lifetime            = "2419200"
   ```
   - If you use a Postgresql database, you must install the `pdo_pgsql` extension for PHP.


4. Start the app
      ```shell
   # With local install
   php composer.phar start
   
   # With global install
   composer start
   ```

5. Use it : [localhost:8101](http://localhost:8101/)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- USAGE EXAMPLES -->

## Usage

The API does not have a graphical interface, so it must be used with tools like [Postman](https://www.postman.com/) for
example.

### Endpoints

The complete list of API breakpoints is available in the [Documentation](https://editor.swagger.io/) with [swagger.yaml](https://github.com/Inge-Info/MSPR_API/blob/main/swagger.yaml).

#### Exemple

* Endpoint for connection
  ```text
  POST /v1/oauth2/token
  ```

* Expected items
  ```json
  {
    "grant_type": "password",
    "email": "email",
    "password": "password"
  }
  ```

* Returned items
  ```json
  {
    "access_token": "0000000000000000000000000000000000000000",
    "expires_in": 3600,
    "token_type": "Bearer",
    "scope": "admin",
    "refresh_token": "0000000000000000000000000000000000000000"
  }
  ```

_For more examples, please refer to the [Documentation](https://editor.swagger.io/) with [swagger.yaml](https://github.com/Inge-Info/MSPR_API/blob/main/swagger.yaml)._

### Unit testing

The API also has a series of unit tests to test and validate the coded functionality.

```shell
# With local install
php composer.phar test
php composer.phar test:coverage
   
# With global install
composer test
composer test:coverage
```

* `test` executes unit tests.
* `test:coverage` executes unit tests and generate test coverage.

<p align="right">(<a href="#top">back to top</a>)</p>


<!-- ROADMAP -->

## Roadmap

- [x] Communication with the database
- [x] OAuth2 login system
    - [x] with `client_id` and `client_secret`
    - [x] with `email` and `password`
    - [x] Permissions
    - [x] Session expiration
- [x] Middlewares
- [x] Manage exceptions, HTTP errors and success codes
- [x] Controllers
  - [x] Sessions
  - [x] Administrators
  - [x] Users
  - [x] Emails
  - [x] Logs
- [x] Unit testing (with coverage)
    - [x] HTTP Codes
    - [x] Controllers
- [x] Documentation
- [x] Config file

<p align="right">(<a href="#top">back to top</a>)</p>
