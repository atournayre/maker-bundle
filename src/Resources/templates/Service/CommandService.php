<?php
declare(strict_types=1);

namespace App\Service;

use App\Attribute\CommandService as AttributeCommandService;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Contracts\VO\ContextInterface;
use App\Helper\AttributeHelper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Webmozart\Assert\Assert;

final class CommandService implements CommandServiceInterface
{
	function __construct(
		private readonly LoggerInterface $logger,
		#[TaggedIterator(TagCommandServiceInterface::class)]
		private readonly iterable $services = [],
	) {
	}


	/**
	 * @throws \Exception
	 */
	public function execute($object, ContextInterface $context, ?string $service = null): void
	{
		if (!$this->supports($object, $service)) {
		    return;
		}

		$service ??= $this->getServiceName($object);

		$services = $this->getServices();

		Assert::keyExists($services, $service, sprintf('Service %s not found', $service));

		$this->doExecute($service, $object, $context);
	}


	/**
	 * @throws \Exception
	 */
	private function doExecute(string $service, $object, ContextInterface $context): void
	{
		$serviceClass = $this->getServices()[$service];
		Assert::methodExists($serviceClass, 'execute');

		$serviceReflection = new \ReflectionClass($service);

		$this->logger->start();
		try {
		    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
		        $serviceClass->preConditionsChecks($object, $context);
		    }
		    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
		        $serviceClass->failFast($object, $context);
		    }

		    $serviceClass->execute($object, $context);

		    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
		        $serviceClass->postConditionsChecks($object, $context);
		    }

		    $this->logger->success();
		    $this->logger->end();
		} catch (\Exception $e) {
		    $this->logger->exception($e);
		    $this->logger->end();
		    throw $e;
		}
	}


	private function supports($object, ?string $service = null): bool
	{
		$service ??= $this->getServiceName($object);

		if ($service === '' || $service === '0') {
		    return false;
		}

		if (!class_exists($service)) {
		    return false;
		}

		return (new \ReflectionClass($service))
		    ->implementsInterface(TagCommandServiceInterface::class);
	}


	private function getServiceName($object): string
	{
		$serviceName = AttributeHelper::getParameter($object, AttributeCommandService::class, 'serviceName');

		if ($serviceName === null || $serviceName === '' || $serviceName === '0') {
		    throw new \LogicException(sprintf('The Value Object %s requested a CommandService but does not have the attribute CommandService.', $object::class));
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

		$servicesNames = array_map(fn($object) => $object::class, $services);

		return array_combine($servicesNames, $services);
	}
}
