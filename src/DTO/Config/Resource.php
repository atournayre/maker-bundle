<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Resource
{
    public function __construct(
        /** @var array<string, string> */
        public readonly array $primitivesMapping,
        /** @var array<string> */
        public readonly array $resources,
        /** @var array<string> */
        public readonly array $exclude,
    )
    {
    }

    /**
     * @param array<string, array<string, string>> $resource
     * @return self
     */
    public static function fromArray(array $resource): self
    {
        return new self(
            $resource['primitives_mapping'] ?? [],
            $resource['resources'] ?? [],
            $resource['exclude'] ?? [],
        );
    }
}
