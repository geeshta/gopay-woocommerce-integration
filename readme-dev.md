# GoPay WooCommerce Integration

## Table of Contents

- [About the Project](#about-the-project)
  - [Built With](#built-with)
- [Development](#development)
  - [Prerequisites](#prerequisites)
  - [Installation](#instalation)
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

- [PHP](https://www.php.net)
- [WordPress](https://wordpress.org/)
- [WooCommerce](https://woocommerce.com)
- [WooCommerce Subscriptions](https://woocommerce.com/document/subscriptions/)*
- [Docker Desktop](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/) _(is part of Docker Desktop)_

###### * *WooCommerce Subscriptions must be installed if you need to deal with recurring payments.*

### Instalation

### Run project

For local project execution, first install WordPress and WooCommerce, then upload and configure the plugin by following the steps below:
1. Copy the plugin files to the '/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the Plugins screen in WordPress.
3. Configure the plugin by providing goid, client id and secret to load the other options (follow these [steps](https://help.gopay.com/en/knowledge-base/gopay-account/gopay-business-account/signing-in-password-reset-activating-and-deactivating-the-payment-gateway/how-to-activate-the-payment-gateway) to activate the payment gateway and get goid, client id and secret).
4. Finally, choose the options you want to be available in the payment gateway (payment methods and banks must be enabled in your GoPay account).

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

Use Composer inside Docker container to install or upgrade dependencies.

Run docker-compose.

```sh
make run-dev
```

Run update.

```sh
make update
```

See `makefile` for more commands.

### Coding Standard

The project contains [WordPress Coding Standards for PHP_CodeSniffer](https://github.com/WordPress/WordPress-Coding-Standards). Use Docker container to check code.

Run docker-compose.

```sh
make run-dev
```

Check and fix code.

```sh
make format-check
```

```sh
make format-fix
```

See `makefile` for more commands.

### Testing

## Versioning

This plugin uses [SemVer](http://semver.org/) for versioning scheme.

### Contribution

- `master` - contains production code. You must not make changes directly to the master!
- `staging` - contains staging code. Pre-production environment for testing.
- `development` - contains development code.

### Contribution process in details

1. Use the development branch for the implementation.
2. Update corresponding readmes after the completion of the development.
3. Create a pull request and properly revise all your changes before merging.
4. Push into the development branch.
5. Upload to staging for testing.
6. When the feature is tested and approved on staging, pull you changes to master.

## Deployment

This plugin uses [Git Updater](https://github.com/afragen/git-updater/) to manage updates.

Before deploy change Version in the `woocommerce-gopay.php`, then commit & push. Staging site uses staging branch.

To fetch the new update:

- Git Updater:
  1) Log into WP admin.
  2) Go to Settings > Git Updater
  3) Click Refresh Cache
  4) Go to Plugins and Update plugin
- WP checks for new version every 12 hours based on the latest versions hosted on WordPress.org.
- You can force the update on the plugin's page by using the "Check for updates" action.
- Or you can download the latest version from the [GitHub repository](https://github.com/argo22packages/gopay-woocommerce-integration) and install it manually by clicking on "Add New" and "Upload Plugin".

## Documentation

## Other useful links
