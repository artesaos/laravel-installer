## Laravel Installer
[![Build Status](https://travis-ci.org/artesaos/laravel-installer.svg?branch=master)](https://travis-ci.org/artesaos/laravel-installer) [![Latest Stable Version](https://poser.pugx.org/artesaos/laravel-installer/v/stable)](https://packagist.org/packages/artesaos/laravel-installer) [![Total Downloads](https://poser.pugx.org/artesaos/laravel-installer/downloads)](https://packagist.org/packages/artesaos/artesaos/laravel-installer) [![Latest Unstable Version](https://poser.pugx.org/artesaos/laravel-installer/v/unstable)](https://packagist.org/packages/artesaos/laravel-installer) [![License](https://poser.pugx.org/artesaos/laravel-installer/license)](https://packagist.org/packages/artesaos/laravel-installer)

This laravel installer is an alternative to the Laravel installer and much like the Symfony installer lets you choose a specific version to install.

> Remember to remove your old laravel installer to prevent conflicts
```bash
composer g remove laravel/installer
```

#### Installation

```bash
composer g require artesaos/laravel-installer
```

#### Usage

This installer works like the default laravel installer. The difference is you can choose the Laravel Version version.
```
laravel new name version
```

The option `--interactive` is available. It will ask for packages to require on your project

Replace `name` for your project name and `version` with a valid laravel version, in any format supported by composer.

Some examples:

```bash
laravel new blog 9.3.1
laravel new blog ~5.5.0
laravel new blog ^7.1
laravel new blog master
```

You can use the help command for instructions:

```
laravel help new
```

#### Changelog

You can view the latest changes [here](https://github.com/artesaos/laravel-installer/blob/master/CHANGELOG.md)
