<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class defines the configuration for the ElegantMakerBundle.
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('elegant_maker');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('namespace_prefix')
                    ->defaultValue('App')
                    ->info('The namespace prefix for generated classes')
                ->end()
                ->scalarNode('dir_prefix')
                    ->defaultValue('src')
                    ->info('The directory prefix for generated files')
                ->end()
                ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_namespace')
                            ->defaultValue('App\Exception')
                            ->info('The namespace for generated exceptions')
                        ->end()
                        ->scalarNode('target_directory')
                            ->defaultValue('src/Exception')
                            ->info('The directory for generated exceptions')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
