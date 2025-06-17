# Class Generator

The Class Generator creates a simple PHP class with customizable namespace, parent class, interfaces, and description.

## Command

```bash
php bin/console make:atournayre:class <name> [options]
```

## Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the class to create (e.g., `User`) |

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--namespace` | The namespace of the class | Empty (uses the configured namespace prefix) |
| `--extends` | The parent class to extend from | None |
| `--implements` | The interfaces to implement (can be used multiple times) | None |
| `--description` | The class description for PHPDoc | None |

## Examples

### Basic Usage

```bash
php bin/console make:atournayre:class User
```

This will generate a simple class:

```php
<?php

namespace App;

/**
 * User class.
 */
class User
{
}
```

### With Custom Namespace

```bash
php bin/console make:atournayre:class User --namespace="App\Domain\Model"
```

This will generate:

```php
<?php

namespace App\Domain\Model;

/**
 * User class.
 */
class User
{
}
```

### With Parent Class

```bash
php bin/console make:atournayre:class Admin --extends="App\Domain\Model\User"
```

This will generate:

```php
<?php

namespace App;

use App\Domain\Model\User;

/**
 * Admin class.
 */
class Admin extends User
{
}
```

### With Interfaces

```bash
php bin/console make:atournayre:class Repository --implements="App\Domain\Repository\RepositoryInterface" --implements="Countable"
```

This will generate:

```php
<?php

namespace App;

use App\Domain\Repository\RepositoryInterface;
use Countable;

/**
 * Repository class.
 */
class Repository implements RepositoryInterface, Countable
{
}
```

### With Description

```bash
php bin/console make:atournayre:class User --description="Represents a user in the system"
```

This will generate:

```php
<?php

namespace App;

/**
 * User class.
 * Represents a user in the system
 */
class User
{
}
```

## Generated File Location

The generated file will be placed in the directory corresponding to the namespace, relative to the configured directory prefix.

For example, if the namespace is `App\Domain\Model` and the directory prefix is `src`, the file will be created at `src/Domain/Model/User.php`.
