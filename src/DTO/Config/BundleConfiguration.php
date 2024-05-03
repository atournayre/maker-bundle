<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class BundleConfiguration
{
    public function __construct(
        public readonly string $rootNamespace,
        public readonly Namespaces $namespaces,
        public readonly Resources $resources,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['root_namespace'],
            Namespaces::fromArray($data['namespaces']),
            Resources::fromArray($data['resources']),
        );
    }
}