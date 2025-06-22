# Controller Generator

The Controller Generator creates a new controller class with customizable namespace, route pattern, and template path. It can optionally extend Symfony's AbstractController.

## Command

```bash
php bin/console make:elegant:controller [name] [options]
```

## Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the controller to create (without the "Controller" suffix) |

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--namespace` | The namespace of the controller | Value from configuration |
| `--extends-abstract-controller` | Whether the controller should extend AbstractController | false |
| `--template` | The template to use for generating the controller | Value from configuration |

## Interactive Prompts

The Controller Generator is fully interactive and will prompt you for the following information:

| Prompt | Description |
|--------|-------------|
| Controller name | The name of the controller to create (without the "Controller" suffix) |
| Extend AbstractController | Whether the controller should extend Symfony's AbstractController |
| Confirmation | Confirm the namespace, directory, file path, route pattern, route name, and template path before generation |

## Features

- Automatically adds "Controller" suffix to the class name
- Generates a route pattern based on the controller name (converted to kebab-case)
- Generates a route name based on the controller name (converted to snake_case)
- Generates a template path based on the controller name (converted to snake_case)
- Configurable namespace and directory through bundle configuration
- Option to extend Symfony's AbstractController
- Customizable controller template through configuration or command option
- Support for implementing interfaces specified in configuration

## Example Usage

### Interactive Mode

```bash
php bin/console make:elegant:controller

 Choose controller name (without "Controller" suffix):
 > Payment

 Should the controller extend Symfony\Bundle\FrameworkBundle\Controller\AbstractController? (yes/no) [no]:
 > no

 Summary
+----------------------------+------------------------------------------+
| Property                   | Value                                    |
+----------------------------+------------------------------------------+
| Controller name            | PaymentController                        |
| Extends AbstractController | No                                       |
| Namespace                  | App\Controller                           |
| Directory                  | src/Controller                           |
| File path                  | src/Controller/PaymentController.php     |
| Route pattern              | /payment                                 |
| Route name                 | payment                                  |
| View template path         | payment/index.html.twig                  |
| Controller template        | controller.tpl.php                       |
| Interfaces                 | None                                     |
+----------------------------+------------------------------------------+

 Do you want to generate this controller class? (yes/no) [yes]:
 > yes

 [OK] Controller class generated successfully!
 Path: src/Controller/PaymentController.php
```

### Command Line Arguments

You can also specify the controller name and options directly on the command line:

```bash
php bin/console make:elegant:controller Payment --namespace="App\Controller\Payment" --extends-abstract-controller
```

### Using a Custom Template

You can specify a custom template to use for generating the controller:

```bash
php bin/console make:elegant:controller Payment --template="my_custom_controller.tpl.php"
```

 Summary
+----------------------------+------------------------------------------+
| Property                   | Value                                    |
+----------------------------+------------------------------------------+
| Controller name            | PaymentController                        |
| Extends AbstractController | Yes                                      |
| Namespace                  | App\Controller\Payment                   |
| Directory                  | src/Controller                           |
| File path                  | src/Controller/PaymentController.php     |
| Route pattern              | /payment                                 |
| Route name                 | payment                                  |
| View template path         | payment/index.html.twig                  |
| Controller template        | controller.tpl.php                       |
| Interfaces                 | None                                     |
+----------------------------+------------------------------------------+

 Do you want to generate this controller class? (yes/no) [yes]:
 > yes

 [OK] Controller class generated successfully!
 Path: src/Controller/PaymentController.php
```

## Generated Code

The generated controller class will look like this:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Atournayre\Contracts\Context\ContextInterface;
use Atournayre\Contracts\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }

    #[Route(path: '/payment', name: 'payment', methods: ['GET', 'POST'])]
    #[Template(template: 'payment/index.html.twig')]
    public function __invoke(Request $request, ContextInterface $context)
    {
        return TryCatch::with(function () use ($request) {

        }, $this->logger)
        // implements catch if needed
        ->execute();
    }
}
```

If you choose to extend AbstractController, the generated code will include the appropriate use statement and extend the AbstractController class:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Atournayre\Contracts\Context\ContextInterface;
use Atournayre\Contracts\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PaymentController extends AbstractController
{
    // ...
}
```

### Controller with Interfaces

When interfaces are specified in the configuration, the generated controller will implement those interfaces:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Atournayre\Contracts\Context\ContextInterface;
use Atournayre\Contracts\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Contracts\Controller\ControllerInterface;
use App\Contracts\Controller\ApiControllerInterface;

final class PaymentController implements ControllerInterface, ApiControllerInterface
{
    // ...
}
```

## Configuration

You can customize the namespace, directory, template path, and interfaces for generated controllers in your configuration:

```yaml
# config/packages/elegant_maker.yaml
elegant_maker:
    controller:
        root_namespace: 'App\Controller'
        target_directory: 'src/Controller'
        template_path: 'controller.tpl.php'  # Custom template path
        interfaces:     # List of interfaces to implement
            - 'App\Contracts\Controller\ControllerInterface'
            - 'App\Contracts\Controller\ApiControllerInterface'
```

### Custom Template

You can create a custom template for your controllers by copying the default template and modifying it to suit your needs. The template is a PHP file with Twig-like syntax that's processed by the Twig engine.

### Implementing Interfaces

When you specify interfaces in the configuration, the generator will automatically:
1. Add use statements for the interfaces
2. Add the interfaces to the class declaration
3. Show the interfaces in the summary before generation

This allows you to ensure that all generated controllers implement the required interfaces for your application.
