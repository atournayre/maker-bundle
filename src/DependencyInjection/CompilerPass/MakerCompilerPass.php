<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection\CompilerPass;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to register all maker services.
 */
class MakerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Find all services tagged with elegant.maker
        $taggedServices = $container->findTaggedServiceIds('elegant.maker');

        // Register all maker services that implement MakerInterface
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!$definition->isAutoconfigured()) {
                continue;
            }

            $class = $definition->getClass();
            if (!$class) {
                continue;
            }

            if (!class_exists($class)) {
                continue;
            }

            if (is_a($class, MakerInterface::class, true) && !isset($taggedServices[$id])) {
                $definition->addTag('elegant.maker');
            }
        }
    }
}
