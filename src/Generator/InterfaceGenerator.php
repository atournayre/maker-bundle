<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Bundle\MakerBundle\Generator;

class InterfaceGenerator
{
    public function __construct(
        private readonly Generator $generator,
        private readonly string    $rootNamespace,
    )
    {
    }

    public function generate(string $namespacePath, string $name): void
    {
        $path = new NamespacePath($namespacePath, $this->rootNamespace);
        $name = NamespacePath::normalize($name);

        throw new \RuntimeException('Not implemented yet');

        $this->generator->writeChanges();
    }
}
