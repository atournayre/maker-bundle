<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class BundleConfiguration
{
    public function __construct(
        public readonly string $rootNamespace,
        public readonly Namespaces $namespaces,
        public readonly Resources $resources,
        public readonly Directories $directories,
    )
    {
    }

    /**
     * @param array{root_namespace: string, namespaces: array<string, string|null>, resources: array<string, array<string, array<string, string>>>, directories: array<string, string>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['root_namespace'],
            Namespaces::fromArray($data['namespaces']),
            Resources::fromArray($data['resources']),
            Directories::fromArray($data['directories']),
        );
    }
}
