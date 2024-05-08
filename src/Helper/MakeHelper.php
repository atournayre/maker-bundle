<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use Symfony\Bundle\MakerBundle\DependencyBuilder;

final class MakeHelper
{
    /**
     * @param DependencyBuilder $dependencyBuilder
     * @param array<string, string> $dependencies
     * @return void
     */
    public static function configureDependencies(DependencyBuilder $dependencyBuilder, array $dependencies): void
    {
        foreach ($dependencies as $class => $package) {
            $dependencyBuilder->addClassDependency($class, $package);
        }
    }
}
