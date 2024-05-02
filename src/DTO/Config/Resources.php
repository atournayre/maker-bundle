<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Resources
{
    public function __construct(
        public readonly Resource $collection,
        public readonly Resource $dto,
        public readonly Resource $valueObject,
    )
    {
    }

    public static function fromArray(array $resources): self
    {
        return new self(
            Resource::fromArray($resources['collection'] ?? []),
            Resource::fromArray($resources['dto'] ?? []),
            Resource::fromArray($resources['value_object'] ?? []),
        );
    }
}
