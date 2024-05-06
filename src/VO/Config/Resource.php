<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Config;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Service\FilesystemService;

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

    /**
     * @return string[]
     */
    public function allowedTypes(FilesystemService $filesystem): array
    {
        return Map::from($this->primitivesMapping)
            ->merge($filesystem->findFilesInDirectory($this->resources, $this->exclude))
            ->values()
            ->unique()
            ->toArray();
    }
}
