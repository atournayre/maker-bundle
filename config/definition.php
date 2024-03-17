<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#configuration
 */
return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->scalarNode('skeleton_dir')->defaultValue(dirname(__DIR__).'/config/skeleton')->end()
            ->scalarNode('root_dir')->defaultValue('src')->end()
            ->scalarNode('root_namespace')->defaultValue('App')->end()
        ->end()
    ;
};
