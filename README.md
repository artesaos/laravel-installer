## Laravel Installer
[![Build Status](https://travis-ci.org/artesaos/laravel-installer.svg?branch=master)](https://travis-ci.org/artesaos/laravel-installer) [![Latest Stable Version](https://poser.pugx.org/artesaos/laravel-installer/v/stable)](https://packagist.org/packages/artesaos/laravel-installer) [![Total Downloads](https://poser.pugx.org/artesaos/laravel-installer/downloads)](https://packagist.org/packages/artesaos/artesaos/laravel-installer) [![Latest Unstable Version](https://poser.pugx.org/artesaos/laravel-installer/v/unstable)](https://packagist.org/packages/artesaos/laravel-installer) [![License](https://poser.pugx.org/artesaos/laravel-installer/license)](https://packagist.org/packages/artesaos/laravel-installer)

This laravel installer is an alternative like a symfony installer, where you could choose a specific version to install.

> Remember to remove your old laravel installer for prevent conflicts
```bash
composer g remove laravel/installer
```

#### Installation

```bash
composer g require artesaos/laravel-installer
```

#### Usage

This installer works like a default laravel installer. The difference is you can choose your version.
```
laravel new name version
```

The option `--interactive` is available. It will ask for packages to require on your project

Replace `name` for your project name and `version` for one of the available versions:

`4.2`

`5.0`

`5.1` - You can use `LTS` instead

`5.2`

`5.3`

`5.4` - Default version

`master` - Install from the current master branch

`develop` - Install the development version from the next release

You can use the help command for instructions:
```
laravel help new
```

#### Changelog

You can view the latest changes [here](https://github.com/artesaos/laravel-installer/blob/master/CHANGELOG.md)
