# GoPay WooCommerce Integration

## Table of Contents

- [About the Project](#about-the-project)
  - [Built With](#built-with)
- [Development](#development)
  - [Prerequisites](#prerequisites)
  - [Instalation](#instalation)
  - [Run project](#run-project)
  - [Project Structure](#project-structure)
  - [Migrations](#migrations)
  - [Dependencies](#dependencies)
  - [Coding Standard](#coding-standard)
  - [Testing](#testing)
- [Versioning](#versioning)
- [Deployment](#deployment)
- [Documentation](#documentation)
- [Other useful links](#other-useful-links)

## About The Project

GoPay payment gateway integration with the WooCommerce plugin eCommerce platform built on WordPress.

### Built With

- [GoPay's PHP SDK for Payments REST API](https://github.com/gopaycommunity/gopay-php-api)
- [Composer](https://getcomposer.org/)

## Development

Running project on local machine for development and testing purposes.

### Prerequisites

- [WordPress](https://wordpress.org/)
- [Composer](https://getcomposer.org/)

### Instalation

### Run project

### Project Structure

- **`admin`**
  - **`css`**
  - **`js`**
  - **`views`**
- **`includes`**
  - **`assets`**
- **`languages`**
- **`vendor`**
- **`readme-dev.md`**

### Migrations

### Dependencies

Use Composer to install or upgrade dependencies.

### Coding Standard

The project contains [WordPress Coding Standards for PHP_CodeSniffer](https://github.com/WordPress/WordPress-Coding-Standards).

First install composer with dev dependencies.

```sh
make composer-dev
```

Check and fix code.

```sh
make format-check
```

```sh
make format-fix
```

### Testing

## Versioning

We use [SemVer](http://semver.org/).

## Deployment

We use [Git Updater](https://github.com/afragen/git-updater/).

Before deploy change Version in the `woocommerce-gopay.php` and push. Staging site uses staging branch.

1) Log into WP admin.
2) Go to Settings > Git Updater
3) Click Refresh Cache
4) Go to Plugins and Update plugin

## Documentation

## Other useful links
