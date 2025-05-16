# Craft Project Boilerplate

The goal of this project is to provide a fully working "boilerplate" (empty skeleton project) using the CraftCMS project.

# Table of Content

- [How to Install](#how-to-install)
    - [1. Installing the boilerplate](#1-installing-the-boilerplate)
    - [2. Setup your local environment](#2-setup-your-local-environment)
    - [3. Setup Vite](#3-setup-vite)
    - [4. Setup front-end tools](#4-setup-front-end-tools)
    - [5. Test your installation](#5-test-your-installation)
- [Dependencies and requirements](#dependencies-and-requirements)
- [Development](#development)
    - [Development dependencies](#development-dependencies)
    - [Authors](#authors)

# How to Install

To start a CraftCMS project with this Boilerplate, simply:

## 1. **Installing the boilerplate**

Charcoal uses the Composer `create-project` command to install the boilerplate:

```shell
composer create-project --prefer-dist locomotivemtl/craft-boilerplate acme
```

## 2. **Setup your local environment**

### Using Laravel Valet

```shell
# Move into your project's directory
cd acme

# Setup valet to use PHP 8.2 only for this project
valet isolate 8.2

# Install the dependencies
valet composer install

# Run the valet installer.
valet php craft install

# Enable the Vite plugin
php craft plugin/install vite
```

### Using DDEV

> WIP

## 3. **Setup Vite**

@todo Quick description of what is Vite and what problems it solves.

Add vite configurations to .env file

```dotenv
VITE_SERVER_URL="http://localhost"
VITE_SERVER_PORT=5173
# Make sure to set the environment to 'dev'. Otherwise, Craft won't be looking for the Vite server.
CRAFT_ENVIRONMENT=dev
```

## 4. **Setup front-end tools**

```shell
npm install
```

## 5. **Test your installation**

Start your Vite server

```shell
npm run dev
```

Visit your project locally http://acme.test

# Dependencies and Requirements

- [`PHP 8.2+`](http://php.net)
    - `ext-json`
    - `ext-pdo`
    - `ext-spl`
    - `ext-mbstring`

# Development

## Development dependencies

- [`Node v20.14`](https://nodejs.org/en/blog/release/v20.14.0https://nodejs.org/en/blog/release/v20.14.0)
- [`NPM v10.8.2`](https://www.npmjs.com/)

## Authors

- [Locomotive, a Montreal Web agency](https://locomotive.ca)
