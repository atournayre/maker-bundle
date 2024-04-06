# Project Getting Started

## Command

```shell
php bin/console project:getting-started
```

## Description

This command will create files and directories to start a project.

Creates the following files:
- src/ArgumentValueResolver/ContextArgumentValueResolver.php
- src/Attribute/CommandService.php
- src/Attribute/QueryService.php
- src/Contracts/Logger/LoggerInterface.php
- src/Contracts/Response/ResponseInterface.php
- src/Contracts/Routing/RoutingInterface.php
- src/Contracts/Security/SecurityInterface.php
- src/Contracts/Security/UserInterface.php
- src/Contracts/Service/CommandServiceInterface.php
- src/Contracts/Service/FailFastInterface.php
- src/Contracts/Service/PostConditionsChecksInterface.php
- src/Contracts/Service/PreConditionsChecksInterface.php
- src/Contracts/Service/QueryServiceInterface.php
- src/Contracts/Service/TagCommandServiceInterface.php
- src/Contracts/Service/TagQueryServiceInterface.php
- src/Contracts/Templating/TemplatingInterface.php
- src/Contracts/Type/Primitive/ScalarObjectInterface.php
- src/Exception/FailFast.php
- src/Factory/ContextFactory.php
- src/Helper/AttributeHelper.php
- src/Logger/AbstractLogger.php
- src/Logger/DefaultLogger.php
- src/Logger/NullLogger.php
- src/Service/CommandService.php
- src/Service/QueryService.php
- src/Service/Response/SymfonyResponseService.php
- src/Service/Routing/SymfonyRoutingService.php
- src/Service/Security/SymfonySecurityService.php
- src/Service/Templating/TwigTemplatingService.php
- src/Trait/EntityIsTrait.php
- src/Trait/IdEntityTrait.php
- src/Trait/IsTrait.php
- src/Type/Primitive/AbstractCollectionType.php
- src/Type/Primitive/BooleanType.php
- src/Type/Primitive/IntegerType.php
- src/Type/Primitive/ListImmutableType.php
- src/Type/Primitive/ListType.php
- src/Type/Primitive/MapImmutableType.php
- src/Type/Primitive/MapType.php
- src/Type/Primitive/StringType.php
- src/VO/Context.php
- src/VO/DateTime.php
- src/VO/Null/NullUser.php

## Usage

### Decoupling from Symfony

Following interfaces are provided to decouple from Symfony:
- Logger/LoggerInterface (2 implementations: NullLogger, DefaultLogger)
- Response/ResponseInterface (1 implementation: SymfonyResponseService)
- Routing/RoutingInterface (1 implementation: SymfonyRoutingService)
- Security/SecurityInterface (1 implementation: SymfonySecurityService)
- Templating/TemplatingInterface (1 implementation: TwigTemplatingService)


### Controller

A type hinted argument can be used to get the context.

```php
<?php
namespace App\Controller;

use App\VO\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AcmeController extends AbstractController
{
    public function index(Context $context, AcmeService $acme): Response
    {
        // ...
        $user = $context->user();
        $createdAt = $context->createdAt()->toDateTime();
        // ...
        $acme->doSomething($context);
        $data = $acme->fetchSomething($context);
        // ...
    }
}
```

See the Value Object Context to have more information.

### Logger

#### NullLogger

```yaml
# config/services.yaml
services:
    app.logger.null: '@App\Logger\NullLogger'
```

```php
// your controller (service, etc.)
public function index(
    #[Autowire(service: 'app.logger.null')] LoggerInterface $logger,
): Response
{
    $logger->info('Hello world');
    return new Response('<body></body>');
}
```

Logs are disabled.


#### DefaultLogger


`DefaultLogger` implements `\Psr\Log\LoggerInterface` and add the following methods:`exception()`, `start()`, `end()`, `success()`, `failFast()` 

By default messages are prefixed by the class name of the logger.
```console
[App\Logger\DefaultLogger] Hello world
```

To customize the prefix, use the `calls` key in the service definition.
    
```yaml
# config/services.yaml
services:
    app.logger.acme:
        class: App\Logger\DefaultLogger
        calls:
            - [setLoggerIdentifier, ['acme']]
```
    
```php
// your controller (service, etc.)
public function index(
    #[Autowire(service: 'app.logger.acme')] LoggerInterface $logger,
): Response
{
    $logger->info('Hello world');
    return new Response('<body></body>');
}
```

```console
[acme] Hello world
```

#### Custom logger
Create a logger, see [specific documentation](new-logger.md).

### Primitive types

To be more DDD compliant, primitive types are provided.

- BooleanType
- IntegerType
- StringType
- ListType
- ListImmutableType
- MapType
- MapImmutableType

There is no FloatType on purpose, use IntegerType instead, then use ->toFloat().

Create your own types by extending from the type you need.


### Service
Create a service, see [specific documentation](new-service.md).

### Value Object

#### Context
Context should always be injected (in controllers, commands, queries, services, etc.).

A Context (value object) is available as an argument in controllers thanks to Symfony's ArgumentValueResolver.

Anywhere else, you can use the ContextFactory to create a Context.

A Context is a collection of values:
- user (UserInterface)
- createdAt (DateTime - a value object with specifics methods)


#### DateTime

A DateTime is a value object with specifics methods.

Do not confuse with the PHP DateTime class.

DateTime as a method toDateTime() to get the PHP DateTime object.

#### Null

Use the null object pattern to avoid null checks.
