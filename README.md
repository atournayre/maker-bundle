# MakerBundle

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
# config/packages/atournayre_maker.yaml

atournayre_maker:
    root_namespace: 'App'
```

## Documentation

### Commands

Legend:
- ⚠️: Experimental command.
- 🚧: Command to be implemented.
- ✅: Command implemented.

| Status | Command                       | Description and documentation                                           |
|--------|-------------------------------|-------------------------------------------------------------------------|
| ✅️     | `make:add:events-to-entities` | [Add events to entities](docs/add-events-to-entities.md)                |
| ✅️     | `make:new:command`            | [Create a new Command](docs/new-default.md)                             |
| ⚠️     | `make:new:controller`         | [Create a new Controller](docs/new-default.md)                          |
| ✅️     | `make:new:collection`         | [Create a new Collection](docs/new-default.md)                          |
| ✅      | `make:new:dto`                | [Create a new DTO](docs/new-default.md)                                 |
| ✅      | `make:new:event`              | [Create a new Event and Listener](docs/new-default.md)                  |
| ✅      | `make:new:exception`          | [Create a new Exception](docs/new-default.md)                           |
| ✅      | `make:new:interface`          | [Create a new Interface](docs/new-default.md)                           |
| ✅      | `make:new:logger`             | [Create a new Logger](docs/new-logger.md)                               |
| ✅      | `make:new:service`            | [Create a new Service](docs/new-service.md)                             |
| ✅      | `make:new:trait`              | [Create a new Trait](docs/new-default.md)                               |
| ✅      | `make:new:vo`                 | [Create a new VO](docs/new-default.md)                                  |
| ✅      | `project:getting-started`     | [Add minimal files to start a project](docs/project-getting-started.md) |
