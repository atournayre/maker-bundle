# Installation

This guide will help you install and configure the Atournayre Maker Bundle in your Symfony application.

## Requirements

Before installing the bundle, make sure your system meets the following requirements:

- PHP 8.2 or higher
- Symfony 6.4 or 7.0+
- Composer

## Step 1: Install the Bundle

Install the bundle using Composer:

```bash
composer require atournayre/maker-bundle --dev
```

> Note: We recommend installing this bundle with the `--dev` flag since it's primarily used during development.

## Step 2: Enable the Bundle

If you're using Symfony Flex, the bundle will be automatically enabled. If not, you'll need to enable it manually by adding it to your `config/bundles.php` file:

```php
// config/bundles.php
return [
    // ...
    Atournayre\Bundle\MakerBundle\AtournayeMakerBundle::class => ['dev' => true],
];
```

## Step 3: Configure the Bundle (Optional)

Create a configuration file for the bundle:

```yaml
# config/packages/dev/atournayre_maker.yaml
atournayre_maker:
    namespace_prefix: 'App'  # Default namespace prefix for generated classes
    dir_prefix: 'src'        # Default directory prefix for generated files
```

## Step 4: Verify Installation

To verify that the bundle is correctly installed, run:

```bash
php bin/console list make:atournayre
```

You should see a list of available maker commands provided by the bundle.

## Next Steps

Now that you have installed the bundle, you can:

- Learn how to use the available generators in the [Usage Guide](usage.md)
- Explore the [available generators](generators/index.md)
- Create your own [custom generators](extending/custom-generators.md)
