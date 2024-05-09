<?php
declare(strict_types=1);

namespace App\Service\Routing;

use App\Contracts\Routing\RoutingInterface;
use Symfony\Component\Routing\RouterInterface;

final class SymfonyRoutingService implements RoutingInterface
{
	public function __construct(
		private readonly RouterInterface $router,
	) {
	}


	public function generate(string $name, array $parameters = [], int $referenceType = RoutingInterface::ABSOLUTE_PATH): string
	{
		return $this->router->generate($name, $parameters, $referenceType);
	}
}
