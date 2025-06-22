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
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_namespace')
                            ->defaultValue('App\Controller')
                            ->info('The namespace for generated controllers')
                        ->end()
                        ->scalarNode('target_directory')
                            ->defaultValue('src/Controller')
                            ->info('The directory for generated controllers')
                        ->end()
                        ->scalarNode('template_path')
                            ->defaultValue('controller.tpl.php')
                            ->info('The template path for generated controllers')
                        ->end()
                        ->arrayNode('interfaces')
                            ->prototype('scalar')->end()
                            ->info('List of interfaces that the controller should implement')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('collection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_namespace')
                            ->defaultValue('App\Collection')
                            ->info('The namespace for generated collections')
                        ->end()
                        ->scalarNode('target_directory')
                            ->defaultValue('src/Collection')
                            ->info('The directory for generated collections')
                        ->end()
                        ->arrayNode('allowed_namespaces')
                            ->prototype('scalar')->end()
                            ->defaultValue(['App\DTO', 'App\Entity', 'App\ValueObject'])
                            ->info('List of namespaces that can be used for collection elements')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
