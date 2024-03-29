# MakerBundle

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require atournayre/maker-bundle --dev
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Atournayre\Bundle\MakerBundle\AtournayreMakerBundle::class => ['dev' => true],
];
```

### Step 3: Configure the Bundle

```yaml
# config/services.yaml

services:
    _defaults:
        bind:
            $rootDir: '%kernel.project_dir%'
            $rootNamespace: 'App'
            
    Atournayre\Bundle\MakerBundle\:
        resource: '../vendor/atournayre/maker-bundle/src/'
        exclude:
            - '../vendor/atournayre/maker-bundle/src/DependencyInjection/'
            - '../vendor/atournayre/maker-bundle/src/Resources/'
            - '../vendor/atournayre/maker-bundle/src/AtournayreMakerBundle.php'

    Symfony\Bundle\MakerBundle\Generator: '@maker.generator' 
```

