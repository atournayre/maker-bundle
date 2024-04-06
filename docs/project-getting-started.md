# Project Getting Started

## Command

```shell
php bin/console project:getting-started
```

## Description

This command will create files and directories to start a project.

Creates the following files:
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
- src/VO/Context.php
- src/VO/DateTime.php
- src/VO/Null/NullUser.php
