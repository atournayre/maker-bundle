# Logging

## Files
- Contracts
  - Logger
    - LoggerInterface.php
- Logger
  - AbstractLogger.php
  - DefaultLogger.php
  - NullLogger.php

## Description

`LoggerInterface` extends `\Psr\Log\LoggerInterface` and add the following methods:`exception()`, `start()`, `end()`, `success()`, `failFast()`

By default messages are prefixed by the class name of the logger.
```console
[App\Logger\DefaultLogger] Hello world
```

## Usage

### Disable logs

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


### Change the logger prefix

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
