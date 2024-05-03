<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class MakerBundleConfig
{
    public function __construct(
        #[Autowire('%atournayre_maker.root_namespace%')]
        private readonly string $rootNamespace,
        #[Autowire('%atournayre_maker.namespaces%')]
        private readonly array  $namespaces,
        #[Autowire('%atournayre_maker.resources%')]
        private readonly array  $resources,
    )
    {
    }

    public function __invoke(): BundleConfiguration
    {
        return BundleConfiguration::fromArray([
            'root_namespace' => $this->rootNamespace,
            'namespaces' => $this->namespaces,
            'resources' => $this->resources,
        ]);
    }
}
