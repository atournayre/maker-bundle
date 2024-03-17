<?php

namespace Atournayre\Bundle\MakerBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class AtournayreMakerBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition) : void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
        $container->import('../config/services.php');
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
