# Available Generators

The Elegant Maker Bundle provides several code generators to help you create different types of classes and components based on the `atournayre/framework` library.

## Overview

Each generator is designed to create a specific type of code structure following the architecture and patterns of the `atournayre/framework` library. All generators are accessible through console commands with the prefix `make:elegant:`.

## List of Generators

| Generator | Command | Description |
|-----------|---------|-------------|
| [Class Generator](class.md) | `make:elegant:class` | Creates a simple PHP class |
| [Exception Generator](exception.md) | `make:elegant:exception` | Creates a new exception class |
| More generators coming soon... | | |

## Common Features

All generators share these common features:

- Namespace customization
- Directory path customization
- PHP 8+ features support (attributes, typed properties, etc.)
- Integration with the `atournayre/framework` library

## Using Generators

To use any generator, run its corresponding command in your terminal:

```bash
php bin/console make:elegant:<generator> [arguments] [options]
```

For detailed information about a specific generator, refer to its dedicated documentation page or use the `--help` option:

```bash
php bin/console make:elegant:class --help
```

## Creating Custom Generators

If you need to create custom generators for your specific needs, see the [Custom Generators](../extending/custom-generators.md) guide.
