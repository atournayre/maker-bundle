<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('atournayre_maker');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('skeleton_dir')->defaultValue(dirname(__DIR__).'/config/skeleton')->end()
                ->scalarNode('root_dir')->defaultValue('src')->end()
                ->scalarNode('root_namespace')->defaultValue('App')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
