<?php
declare(strict_types=1);

namespace App\Service;

use App\Attribute\QueryService as AttributeQueryService;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Helper\AttributeHelper;
use App\VO\Context;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Webmozart\Assert\Assert;

final class QueryService implements QueryServiceInterface
{
	#[TaggedIterator(TagQueryServiceInterface::class)]
	function __construct(
		public readonly LoggerInterface $logger,
		public readonly iterable $services = [],
	) {
	}


	/**
	 * @throws \Exception
	 */
	function fetch($object, Context $context, ?string $service = null)
	{
		if (!$this->supports($object, $service)) {
		    return;
		}

		$service ??= $this->getServiceName($object);

		$services = $this->getServices();

		Assert::keyExists($services, $service, sprintf('Service %s not found', $service));

		$serviceClass = $services[$service];
		Assert::methodExists($serviceClass, '__invoke');

		return $this->doQuery($service, $object, $context);
	}


	/**
	 * @throws \Exception
	 */
	private function doQuery(string $service, $object, Context $context)
	{
		$serviceClass = $this->getServices()[$service];
		Assert::methodExists($serviceClass, 'fetch');

		$serviceReflection = new \ReflectionClass($service);

		$this->logger->start();
		try {
		    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
		        $serviceClass->preConditionsChecks($object, $context);
		    }
		    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
		        $serviceClass->failFast($object, $context);
		    }

		    $result = $serviceClass->fetch($object, $context);

		    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
		        $serviceClass->postConditionsChecks($object, $context);
		    }

		    $this->logger->success();
		    $this->logger->end();

		    return $result;
		} catch (\Exception $e) {
		    $this->logger->exception($e);
		    $this->logger->end();
		    throw $e;
		}
	}


	private function supports($object, ?string $service = null): bool
	{
		$service ??= $this->getServiceName($object);

		if (empty($service)) {
		    return false;
		}

		if (!class_exists($service)) {
		    return false;
		}

		return (new \ReflectionClass($service))
		    ->implementsInterface(TagQueryServiceInterface::class);
	}


	private function getServiceName($object): string
	{
		$serviceName = AttributeHelper::getParameter($object, AttributeQueryService::class, 'serviceName');

		if (empty($serviceName)) {
		    throw new \LogicException(sprintf('The Value Object %s requested a QueryService but does not have the attribute QueryService.', get_class($object)));
		}

		return $serviceName;
	}


	private function getServices(): array
	{
		$services = [];

		if (is_array($this->services)) {
		    $services = $this->services;
		}

		if (is_iterable($this->services)) {
		    $services = iterator_to_array($this->services);
		}

		$servicesNames = array_map(fn($object) => get_class($object), $services);

		return array_combine($servicesNames, $services);
	}
}