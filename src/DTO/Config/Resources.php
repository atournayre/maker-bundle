<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Resources
{
    public function __construct(
        public readonly Resource $collection,
        public readonly Resource $dto,
        public readonly Resource $event,
        public readonly Resource $valueObject,
        public readonly Resource $service,
        public readonly Resource $trait,
    )
    {
    }

    public static function fromArray(array $resources): self
    {
        return new self(
            Resource::fromArray($resources['collection'] ?? []),
            Resource::fromArray($resources['dto'] ?? []),
            Resource::fromArray($resources['event'] ?? []),
            Resource::fromArray($resources['value_object'] ?? []),
            Resource::fromArray($resources['service'] ?? []),
            Resource::fromArray($resources['trait'] ?? []),
        );
    }
}
