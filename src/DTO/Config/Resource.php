<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Resource
{
    public function __construct(
        public readonly array $primitivesMapping,
        public readonly array $resources,
        public readonly array $exclude,
    )
    {
    }

    public static function fromArray(array $resource): self
    {
        return new self(
            $resource['primitives_mapping'] ?? [],
            $resource['resources'] ?? [],
            $resource['exclude'] ?? [],
        );
    }
}
