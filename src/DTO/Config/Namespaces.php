<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Namespaces
{
    public function __construct(
        public readonly ?string $adapter = null,
        public readonly ?string $argumentValueResolver = null,
        public readonly ?string $attribute = null,
        public readonly ?string $collection = null,
        public readonly ?string $command = null,
        public readonly ?string $configuration = null,
        public readonly ?string $contracts = null,
        public readonly ?string $controller = null,
        public readonly ?string $dispatcher = null,
        public readonly ?string $dto = null,
        public readonly ?string $entity = null,
        public readonly ?string $event = null,
        public readonly ?string $eventListener = null,
        public readonly ?string $exception = null,
        public readonly ?string $factory = null,
        public readonly ?string $helper = null,
        public readonly ?string $logger = null,
        public readonly ?string $manager = null,
        public readonly ?string $serviceCommand = null,
        public readonly ?string $serviceQuery = null,
        public readonly ?string $trait = null,
        public readonly ?string $traitEntity = null,
        public readonly ?string $type = null,
        public readonly ?string $vo = null,
    )
    {
    }

    public static function fromArray(array $namespaces): self
    {
        return new self(
            $namespaces['adapter'] ?? null,
            $namespaces['argument_value_resolver'] ?? null,
            $namespaces['attribute'] ?? null,
            $namespaces['collection'] ?? null,
            $namespaces['command'] ?? null,
            $namespaces['configuration'] ?? null,
            $namespaces['contracts'] ?? null,
            $namespaces['controller'] ?? null,
            $namespaces['dispatcher'] ?? null,
            $namespaces['dto'] ?? null,
            $namespaces['entity'] ?? null,
            $namespaces['event'] ?? null,
            $namespaces['event_listener'] ?? null,
            $namespaces['exception'] ?? null,
            $namespaces['factory'] ?? null,
            $namespaces['helper'] ?? null,
            $namespaces['logger'] ?? null,
            $namespaces['manager'] ?? null,
            $namespaces['service_command'] ?? null,
            $namespaces['service_query'] ?? null,
            $namespaces['trait'] ?? null,
            $namespaces['trait_entity'] ?? null,
            $namespaces['type'] ?? null,
            $namespaces['vo'] ?? null,
        );
    }
}
