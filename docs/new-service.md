# Create a new service

## Command

```shell
php bin/console make:new:service
```

## Description

Create a new service.

Two types of services are available:
- **Command**
- **Query**

## Usage

First, you need to create (or have) a VO.

```php
<?php
// src/VO/SampleForCommand.php

declare(strict_types=1);

namespace App\VO;

use App\Attribute\CommandService;
use App\Service\Command\SampleCommandService;
use Webmozart\Assert\Assert;

final class SampleForCommand
{
	private string $id;


	private function __construct(string $id)
	{
		$this->id = $id;
	}


	public static function create(string $id): self
	{
		// Assertions...
		return new self($id);
	}

    // Getters, withers

}
```

You can also have a VO for the query.
```php
<?php
// src/VO/SampleForQuery.php

declare(strict_types=1);

namespace App\VO;

use App\Attribute\CommandService;
use App\Service\Command\SampleCommandService;
use Webmozart\Assert\Assert;

final class SampleForQuery
{
	private string $id;


	private function __construct(string $id)
	{
		$this->id = $id;
	}


	public static function create(string $id): self
	{
		// Assertions...
		return new self($id);
	}

    // Getters, withers

}
```

Then, create a new **command** service using command `make:new:service`.

The following code will be generated and all you need to do is implement the logic or remove the method and interface from the class if not needed.

```php
<?php
// src/Service/Command/SampleCommandService.php

declare(strict_types=1);

namespace App\Service\Command;

use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Exception\FailFast;
use App\VO\SampleForCommand;
use App\VO\Context;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(TagCommandServiceInterface::class)]
final readonly class SampleCommandService implements PreConditionsChecksInterface, FailFastInterface, TagCommandServiceInterface, PostConditionsChecksInterface
{
	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 * @param SampleForCommand $object
	 */
	public function preConditionsChecks($object, Context $context): void
	{
	}


	/**
	 * Implement logic here, or remove method and interface from the class if not needed.
	 * @throws FailFast
	 * @param SampleForCommand $object
	 */
	public function failFast($object, Context $context): void
	{
	}


	/**
	 * @throws \Exception
	 * @param SampleForCommand $object
	 */
	public function execute($object, Context $context): void
	{
	}


	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 * @param SampleForCommand $object
	 */
	public function postConditionsChecks($object, Context $context): void
	{
	}
}
```

Now, VO has been updated with a new attribute `CommandService`.

```php
// src/VO/SampleForCommand.php

#[CommandService(serviceName: SampleCommandService::class)]
final class SampleForCommand { /* ... */ }
```

Then, create a new **query** service using command `make:new:service`.

The following code will be generated and all you need to do is implement the logic or remove the method and interface from the class if not needed.

```php
<?php
// src/Service/Query/SampleQueryService.php

declare(strict_types=1);

namespace App\Service\Query;

use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Exception\FailFast;
use App\VO\SampleForQuery;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(TagQueryServiceInterface::class)]
final readonly class SampleQueryService implements PreConditionsChecksInterface, FailFastInterface, TagQueryServiceInterface, PostConditionsChecksInterface
{
	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 * @param SampleForQuery $object
	 */
	public function preConditionsChecks($object, \App\VO\Context $context): void
	{
	}


	/**
	 * Implement logic here, or remove method and interface from the class if not needed.
	 * @throws FailFast
	 * @param SampleForQuery $object
	 */
	public function failFast($object, \App\VO\Context $context): void
	{
	}


	/**
	 * @throws \Exception
	 * @param SampleForQuery $object
	 */
	public function fetch($object, \App\VO\Context $context)
	{
	}


	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 * @param SampleForQuery $object
	 */
	public function postConditionsChecks($object, \App\VO\Context $context): void
	{
	}
}
```

Now, VO has been updated with a new attribute `QueryService`.

```php
// src/VO/SampleForQuery.php

#[QueryService(serviceName: SampleQueryService::class)]
final class SampleForQuery { /* ... */ }
```

Finally, you can use the service.


```php
<?php
// src/Controller/AcmeController.php

namespace App\Controller;

use App\Contracts\Response\ResponseInterface;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Factory\ContextFactory;
use App\VO\Sample;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class AcmeController
{
    public function __construct(
        private ContextFactory          $contextFactory,
        private CommandServiceInterface $commandService,
        private QueryServiceInterface   $queryService,
        private ResponseInterface       $response,
    )
    {
    }

    public function index(): JsonResponse
    {
        $context = $this->contextFactory->create();
        
        // COMMAND
        $voForCommand = SampleForCommand::create('acme');
        
        // Use the command service defined in the VO attribute.
        $this->commandService->execute($voForCommand, $context);
        
        // Force using some specific service for the command.
        $this->commandService->execute($voForCommand, $context, SampleCommandService::class);
        
        // QUERY
        $voForQuery = SampleForQuery::create('acme');

        // Use the query service defined in the VO attribute.
        $data = $this->queryService->fetch($voForQuery, $context);

        // Force using some specific service for the query.
        $data = $this->queryService->fetch($voForQuery, $context, SampleQueryService::class);

        return $this->response->json([]);
    }
}
```

