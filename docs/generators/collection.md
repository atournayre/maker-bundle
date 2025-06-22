# Collection Generator

The Collection Generator creates a new collection class with customizable namespace and implementation of AsListInterface and/or AsMapInterface. It provides a structured way to create type-safe collections for your application.

## Command

```bash
php bin/console make:elegant:collection [name] [class] [options]
```

## Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the collection to create (without the "Collection" suffix) |
| `class` | The name of the class that constitutes the elements of the collection |

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--namespace` | The namespace of the collection | Value from configuration |
| `--list` | Implement AsListInterface | false |
| `--map` | Implement AsMapInterface | false |

## Interactive Prompts

The Collection Generator is fully interactive and will prompt you for the following information:

| Prompt | Description |
|--------|-------------|
| Collection name | The name of the collection to create (without the "Collection" suffix) |
| Class name | The name of the class that constitutes the elements of the collection |
| Confirmation | Confirm the namespace and directory before generation |

## Features

- Automatically adds "Collection" suffix to the class name
- Generates methods for creating collections from lists and/or maps
- Configurable namespace and directory through bundle configuration
- Option to implement AsListInterface and/or AsMapInterface
- Type-safe collections with validation
- Support for autocomplete of class names from allowed namespaces

## Example Usage

### Interactive Mode

```bash
php bin/console make:elegant:collection

 Collection name (without the "Collection" suffix):
 > User

 Class name for collection elements:
 > App\Entity\User

 Summary
+----------------------------+------------------------------------------+
| Property                   | Value                                    |
+----------------------------+------------------------------------------+
| Collection name            | UserCollection                           |
| Class name                 | App\Entity\User                          |
| Namespace                  | App\Collection                           |
| Directory                  | src/Collection                           |
| File path                  | src/Collection/UserCollection.php        |
| Implements AsListInterface | Yes                                      |
| Implements AsMapInterface  | Yes                                      |
+----------------------------+------------------------------------------+

 Do you want to generate this collection class? (yes/no) [yes]:
 > yes

 [OK] Collection class generated successfully!
 Path: src/Collection/UserCollection.php
```

### Command Line Arguments

You can also specify the collection name, class name, and options directly on the command line:

```bash
php bin/console make:elegant:collection User App\\Entity\\User --namespace="App\Collection\User" --list
```

### Implementing Specific Interfaces

You can choose to implement only AsListInterface or AsMapInterface:

```bash
php bin/console make:elegant:collection User App\\Entity\\User --list
```

```bash
php bin/console make:elegant:collection User App\\Entity\\User --map
```

Or both:

```bash
php bin/console make:elegant:collection User App\\Entity\\User --list --map
```

## Generated Code

The generated collection class will look like this:

```php
<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Common\Assert\Assert;
use Atournayre\Contracts\Collection\AsListInterface;
use Atournayre\Contracts\Collection\AsMapInterface;
use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Primitives\Collection;
use Atournayre\Primitives\Traits\Collection as Collection_;
use App\Entity\User;

final class UserCollection implements AsListInterface, AsMapInterface
{
    use Collection_;

    public static function asList(array $collection): self
    {
        Assert::isListOf($collection, User::class);

        return new self(Collection::of($collection));
    }

    public static function asMap(array $collection): self
    {
        Assert::isMapOf($collection, User::class);

        return new self(Collection::of($collection));
    }
}
```

If you choose to implement only AsListInterface, the generated code will only include the `asList` method:

```php
<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Common\Assert\Assert;
use Atournayre\Contracts\Collection\AsListInterface;
use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Primitives\Collection;
use Atournayre\Primitives\Traits\Collection as Collection_;
use App\Entity\User;

final class UserCollection implements AsListInterface
{
    use Collection_;

    public static function asList(array $collection): self
    {
        Assert::isListOf($collection, User::class);

        return new self(Collection::of($collection));
    }
}
```

Similarly, if you choose to implement only AsMapInterface, the generated code will only include the `asMap` method.

## Configuration

You can customize the namespace, directory, and allowed namespaces for generated collections in your configuration:

```yaml
# config/packages/elegant_maker.yaml
elegant_maker:
    collection:
        root_namespace: 'App\Collection'
        target_directory: 'src/Collection'
        allowed_namespaces:
            - 'App\DTO'
            - 'App\Entity'
            - 'App\ValueObject'
```

### Allowed Namespaces

The `allowed_namespaces` configuration option specifies which namespaces can be used for collection elements. This is used for autocomplete when interactively selecting the class name for collection elements.
