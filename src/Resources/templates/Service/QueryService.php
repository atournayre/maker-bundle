<?php
declare(strict_types=1);

namespace App\Service;

use App\Attribute\QueryService as AttributeQueryService;
use App\Contracts\Logger\LoggableInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Contracts\VO\ContextInterface;
use App\Helper\AttributeHelper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Webmozart\Assert\Assert;

final class QueryService implements QueryServiceInterface
{
	public function __construct(
		public readonly LoggerInterface $logger,
        #[TaggedIterator(TagQueryServiceInterface::class)]
		public readonly iterable $services = [],
	) {
	}

	/**
	 * @throws \Exception
	 */
	public function fetch($object, ContextInterface $context, ?string $service = null)
	{
		if (!$this->supports($object, $service)) {
		    return null;
		}

		$service ??= $this->getServiceName($object);

		$services = $this->getServices();

		Assert::keyExists($services, $service, sprintf('Service %s not found', $service));

		return $this->doQuery($service, $object, $context);
	}

	/**
	 * @throws \Exception
	 */
	private function doQuery(string $service, $object, ContextInterface $context)
	{
		$serviceClass = $this->getServices()[$service];
		Assert::methodExists($serviceClass, 'fetch');

		$reflectionClass = new \ReflectionClass($service);

        $this->logger->setLoggerIdentifier($service);

        $logContext = $this->logContext($serviceClass, $object, $context);

        $this->logger->start($logContext);
		try {
		    if ($reflectionClass->implementsInterface(PreConditionsChecksInterface::class)) {
                $this->logger->debug('PreConditionsChecks', $logContext);
		        $serviceClass->preConditionsChecks($object, $context);
		    }

		    if ($reflectionClass->implementsInterface(FailFastInterface::class)) {
                $this->logger->debug('FailFast', $logContext);
		        $serviceClass->failFast($object, $context);
		    }

            $this->logger->debug('Fetch', $logContext);
		    $result = $serviceClass->fetch($object, $context);

		    if ($reflectionClass->implementsInterface(PostConditionsChecksInterface::class)) {
                $this->logger->debug('PostConditionsChecks', $logContext);
		        $serviceClass->postConditionsChecks($object, $context);
		    }

		    $this->logger->success($logContext);
		    $this->logger->end($logContext);
            $this->logger->setLoggerIdentifier(null);

		    return $result;
		} catch (\Exception $exception) {
		    $this->logger->exception($exception, $logContext);
		    $this->logger->end($logContext);
            $this->logger->setLoggerIdentifier(null);
		    throw $exception;
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

        return in_array(QueryServiceInterface::class, (new \ReflectionClass($service))->getInterfaceNames());
	}


	private function getServiceName($object): string
	{
		$serviceName = AttributeHelper::getParameter($object, AttributeQueryService::class, 'serviceName');

		if ($serviceName === null || $serviceName === '' || $serviceName === '0') {
		    throw new \LogicException(sprintf('The Value Object %s requested a QueryService but does not have the attribute QueryService.', $object::class));
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

		$servicesNames = array_map(static fn($object) => $object::class, $services);

		return array_combine($servicesNames, $services);
	}

    private function logContext($serviceClass, $object, ContextInterface $context): array
    {
        $logContext = [
            'service' => $serviceClass,
            'objectClass' => $object::class,
        ];

        if ((new \ReflectionClass($object))->implementsInterface(LoggableInterface::class)) {
            $logContext = array_merge($logContext, $object->toLog());
        }

        if ($context instanceof LoggableInterface) {
            $logContext = array_merge($logContext, $context->toLog());
        }

        return $logContext;
    }
}
