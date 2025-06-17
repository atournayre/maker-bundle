# Atournayre Maker Bundle

A Symfony bundle for generating code based on the atournayre/framework library.

## Overview

The Atournayre Maker Bundle extends Symfony's code generation capabilities by providing commands to generate code that follows the architecture and patterns of the `atournayre/framework` library.

## Features

- Generate PHP classes with proper namespaces and structure
- Support for PHP 8.2+ features including attributes
- Integration with the `atournayre/framework` library
- Extensible architecture for custom generators
- Command-line interface similar to Symfony's MakerBundle

## Requirements

- PHP 8.2+
- Symfony 6.4 or 7.0+
- Composer

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require atournayre/maker-bundle --dev
```

### Step 2: Enable the Bundle

If you're using Symfony Flex, the bundle will be automatically enabled. If not, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Atournayre\Bundle\MakerBundle\AtournayeMakerBundle::class => ['dev' => true],
];
```

### Step 3: Configure the Bundle (Optional)

Create a configuration file for the bundle:

```yaml
# config/packages/dev/atournayre_maker.yaml
atournayre_maker:
    namespace_prefix: 'App'  # Default namespace prefix for generated classes
    dir_prefix: 'src'        # Default directory prefix for generated files
```

## Usage

### Available Commands

List all available commands:

```console
php bin/console list make:atournayre
```

### Generate a Class

```console
php bin/console make:atournayre:class User
```

With custom namespace:

```console
php bin/console make:atournayre:class User --namespace="App\Domain\Model"
```

With parent class:

```console
php bin/console make:atournayre:class Admin --extends="App\Domain\Model\User"
```

With interfaces:

```console
php bin/console make:atournayre:class Repository --implements="App\Domain\Repository\RepositoryInterface" --implements="Countable"
```

With description:

```console
php bin/console make:atournayre:class User --description="Represents a user in the system"
```

## Creating Custom Generators

You can create your own custom generators by extending the `AbstractMaker` class or implementing the `MakerInterface`. See the [documentation](https://atournayre.github.io/maker-bundle/) for more details.

## Documentation

For more detailed documentation, visit [https://atournayre.github.io/maker-bundle/](https://atournayre.github.io/maker-bundle/)

## License

This bundle is released under the MIT License.
