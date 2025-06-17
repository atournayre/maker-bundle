<?php

namespace Atournayre\Bundle\MakerBundle;

use Atournayre\Bundle\MakerBundle\DependencyInjection\CompilerPass\MakerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * ElegantMakerBundle is a Symfony bundle that provides commands to generate code
 * based on the atournayre/framework library.
 */
class ElegantMakerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register compiler passes
        $container->addCompilerPass(new MakerCompilerPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
