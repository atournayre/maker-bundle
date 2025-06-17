# Elegant Maker Bundle

A Symfony bundle for generating code based on the atournayre/framework library.

## Overview

The Elegant Maker Bundle extends Symfony's code generation capabilities by providing commands to generate code that follows the architecture and patterns of the `atournayre/framework` library.

## Features

- Generate PHP classes with proper namespaces and structure
- Support for PHP 8.1+ features including attributes
- Integration with the `atournayre/framework` library
- Extensible architecture for custom generators
- Command-line interface similar to Symfony's MakerBundle

## Requirements

- PHP 8.2+
- Symfony 6.4 or 7.0+
- Composer

## Quick Start

```bash
# Install the bundle
composer require atournayre/maker-bundle --dev

# Generate a class
php bin/console make:elegant:class MyClass --namespace="App\Domain\Model"
```

## Documentation

- [Installation](installation.md)
- [Usage](usage.md)
- [Available Generators](generators/index.md)
- [Extending the Bundle](extending/custom-generators.md)
- [API Reference](api-reference.md)

## License

This bundle is released under the MIT License.
