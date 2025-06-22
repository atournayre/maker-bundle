# Exception Generator

The Exception Generator creates a new exception class with customizable namespace, parent exception, and implements the ThrowableInterface.

## Command

```bash
php bin/console make:elegant:exception [name] [options]
```

## Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the exception to create (without the "Exception" suffix) |

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--namespace` | The namespace of the exception | Value from configuration |

## Interactive Prompts

The Exception Generator is fully interactive and will prompt you for the following information:

| Prompt | Description |
|--------|-------------|
| Exception name | The name of the exception to create (without the "Exception" suffix) |
| Parent exception | The parent exception class to extend from (select from a list) |
| Confirmation | Confirm the namespace, directory, and file path before generation |

## Features

- Automatically adds "Exception" suffix to the class name
- Lists all PHP core exceptions (Exception, RuntimeException, etc.)
- Lists all existing exception classes in your project
- Uses fixed namespaces for ThrowableInterface (Atournayre\Contracts\Exception) and ThrowableTrait (Atournayre\Common\Exception)
- Configurable namespace and directory through bundle configuration

## Example Usage

### Interactive Mode

```bash
php bin/console make:elegant:exception

 Choose exception name (without "Exception" suffix):
 > PaymentFailed

 Choose parent exception:
  [0] Exception
  [1] RuntimeException
  [2] InvalidArgumentException
  [3] LogicException
  [4] CustomBusinessException (from project)
 > 1

 Summary
+-----------------+------------------------------------------+
| Property        | Value                                    |
+-----------------+------------------------------------------+
| Exception name  | PaymentFailedException                   |
| Parent exception| RuntimeException                         |
| Namespace       | App\Exception                            |
| Directory       | src/Exception                            |
| File path       | src/Exception/PaymentFailedException.php |
+-----------------+------------------------------------------+

 Do you want to generate this exception class? (yes/no) [yes]:
 > yes

 [OK] Exception class generated successfully!
 Path: src/Exception/PaymentFailedException.php
```

### Command Line Arguments

You can also specify the exception name and namespace directly on the command line:

```bash
php bin/console make:elegant:exception MyException --namespace="App\Exception\My"

 Choose parent exception:
  [0] Exception
  [1] RuntimeException
  [2] InvalidArgumentException
  [3] LogicException
  [4] CustomBusinessException (from project)
 > 1

 Summary
+-----------------+------------------------------------------+
| Property        | Value                                    |
+-----------------+------------------------------------------+
| Exception name  | MyException                              |
| Parent exception| RuntimeException                         |
| Namespace       | App\Exception\My                         |
| Directory       | src/Exception                            |
| File path       | src/Exception/MyException.php            |
+-----------------+------------------------------------------+

 Do you want to generate this exception class? (yes/no) [yes]:
 > yes

 [OK] Exception class generated successfully!
 Path: src/Exception/MyException.php
```

## Generated Code

The generated exception class will look like this:

```php
<?php

declare(strict_types=1);

namespace App\Exception;

use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Common\Exception\ThrowableTrait;

class PaymentFailedException extends \RuntimeException implements ThrowableInterface
{
    use ThrowableTrait;
}
```

## Configuration

You can customize the namespace and directory for generated exceptions in your configuration:

```yaml
# config/packages/elegant_maker.yaml
elegant_maker:
    exception:
        root_namespace: 'App\Exception'
        target_directory: 'src/Exception'
```
