<div id="top"></div>
<br />

<!-- PROJECT LOGO -->
<div align="center">
<h3 align="center">Pet Feeder Web</h3>

  <p align="center">
    Enterprise Resource Management WebApp.
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
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->

## About The Project

WebApp written with VueJS allowing to manage the whole information system related to the management of sessions, admins,
users, and emails. It offers a complete Web interface with communication to the API to manage the database and its information secured by an OAuth2
authentication and a permissions' system.

### Built With

* [VueJS 3](https://vuejs.org/)
  * [Axios](https://axios-http.com/)
  * [OAuth2](https://github.com/bshaffer/oauth2-server-php)
  * [Notiwind](https://github.com/emmanuelsw/notiwind)
  * [Tailwind](https://tailwindcss.com/)
  * [Fontawesome](https://fontawesome.com/)
  * [ChartJS](https://www.chartjs.org/)
  * [i18n](https://kazupon.github.io/vue-i18n/)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

### Prerequisites (Linux)

* [NodeJS](https://nodejs.org/en/) with [npm](https://www.npmjs.com/)
  ```shell
    curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
    apt-get install -y nodejs
  ```

### Installation

1. Clone the repo
   ```shell
   git clone https://github.com/Inge-Info/MSPR_CRM
   ```

2. Install npm packages
   ```shell
   npm install
   ```

3. Start the development server
      ```shell
   npm run serve
   ```

4. Use it : [localhost:8100](http://localhost:8100/)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- USAGE EXAMPLES -->

## Usage

Like the API, this WebApp allow the administrator to control the entire API through a web interface.
It is composed of 4 sections:
- Users
- Emails
- Administrators
- Logs

Each section has its own components allowing to fully manage the section in question by allowing to add, modify or even delete data.  
**You must be logged in** to have full access to the WebApp features.

<p align="right">(<a href="#top">back to top</a>)</p>


<!-- ROADMAP -->

## Roadmap

- [x] Communication with the API
- [x] OAuth2 login system
  - [x] With `email` and `password`
  - [x] Permissions
  - [x] Session information
  - [x] Session expiration with auto logout
- [x] Pages
  - [x] Users
  - [x] Emails
  - [x] Administrators
  - [x] Logs
  - [x] Statistics
- [x] Notification system
  - [x] Success
  - [x] Error
  - [x] Features blocked by conditions
- [x] Translation

<p align="right">(<a href="#top">back to top</a>)</p>
