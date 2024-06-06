# PayPal Advanced Checkout using PHP
[![Project status: active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)
[![Project tags](https://img.shields.io/github/v/tag/tozielinski/pp-php-advanced?logo=github)](https://github.com/tozielinski/pp-php-advanced/tags)
[![Project license](https://img.shields.io/github/license/tozielinski/pp-php-advanced?logo=github)](https://github.com/tozielinski/pp-php-advanced/LICENSE)
<!-- [![Project contributors](https://img.shields.io/github/contributors/tozielinski/pp-php-sdk-example?logo=github)](https://github.com/tozielinski/pp-php-sdk-example/graphs/contributors) -->
<!-- [![Project build Status](https://badges.netlify.com/api/docsydocs.svg?branch=main)](https://app.netlify.com/sites/docsydocs/deploys) -->
A simple PayPal Advanced Checkout example using PHP

## Usage

You have to create PayPal REST sandbox credentials and copy/move api/config/Config.php.sav to api/config/Config.php. Insert the credentials into api/config/Config.php as client_id and client_secret for the correct environment. To test card payments, using sandbox and the black button, use [test card numbers](https://docs.adyen.com/development-resources/testing/test-card-numbers/).

## Documentation

A running LAMP environment is necessary. Clone the git to the root folder of that installation.
```sh
git clone https://github.com/tozielinski/pp-php-advanced.git
cd pp-php-advanced/api/config
cp Config.php.sav Config.php
```
Now you must add your credentials in Config.php.

## License

This project is licensed under the MIT license - see the [LICENSE.md](https://github.com/tozielinski/pp-php-advanced/blob/main/LICENSE) file for details.
