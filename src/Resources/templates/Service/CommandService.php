<?php
declare(strict_types=1);

namespace App\Service;

use App\Attribute\CommandService as AttributeCommandService;
use App\Contracts\Logger\LoggableInterface;
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
	public function __construct(
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

            $this->logger->debug('Execute', $logContext);
		    $serviceClass->execute($object, $context);

		    if ($reflectionClass->implementsInterface(PostConditionsChecksInterface::class)) {
                $this->logger->debug('PostConditionsChecks', $logContext);
		        $serviceClass->postConditionsChecks($object, $context);
		    }

		    $this->logger->success($logContext);
		    $this->logger->end($logContext);
            $this->logger->setLoggerIdentifier(null);
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

        return in_array(CommandServiceInterface::class, (new \ReflectionClass($service))->getInterfaceNames());
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
