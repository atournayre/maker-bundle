# Usage Guide

This guide explains how to use the Elegant Maker Bundle to generate code for your Symfony application.

## Basic Usage

All commands provided by this bundle follow the naming convention `make:elegant:*`. You can list all available commands by running:

```bash
php bin/console list make:elegant
```

## Command Structure

Most commands follow this general structure:

```bash
php bin/console make:elegant:<generator> <name> [options]
```

Where:
- `<generator>` is the type of code you want to generate
- `<name>` is the name of the class or component you want to create
- `[options]` are additional parameters to customize the generation

## Available Generators

The bundle provides several generators for different types of code:

- [Class Generator](generators/class.md): Generate simple PHP classes
- More generators coming soon...

## Common Options

Most generators support these common options:

- `--namespace`: The namespace for the generated code (defaults to `App`)
- `--dir`: The directory where the code will be generated (defaults to `src`)

## Examples

### Generate a Simple Class

```bash
php bin/console make:elegant:class User
```

This will generate a simple class named `User` in the default namespace (`App`).

### Generate a Class with Custom Namespace

```bash
php bin/console make:elegant:class User --namespace="App\Domain\Model"
```

This will generate a class named `User` in the `App\Domain\Model` namespace.

### Generate a Class that Extends Another Class

```bash
php bin/console make:elegant:class Admin --extends="App\Domain\Model\User"
```

This will generate a class named `Admin` that extends the `User` class.

### Generate a Class that Implements Interfaces

```bash
php bin/console make:elegant:class Repository --implements="App\Domain\Repository\RepositoryInterface" --implements="Countable"
```

This will generate a class named `Repository` that implements both the `RepositoryInterface` and `Countable` interfaces.

## Next Steps

- Learn about specific generators in the [Generators](generators/index.md) section
- Create your own custom generators by following the [Custom Generators](extending/custom-generators.md) guide
