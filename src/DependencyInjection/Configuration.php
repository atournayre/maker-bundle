<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $this->addRootNamespaceSection($rootNode);
        $this->addDirectoriesSection($rootNode);
        $this->addNamespacesSection($rootNode);
        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addRootNamespaceSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->scalarNode('root_namespace')
                    ->defaultValue('App')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
    }

    private function addNamespacesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('namespaces')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')->defaultValue('App\Adapter')->end()
                        ->scalarNode('argument_value_resolver')->defaultValue('App\ArgumentValueResolver')->end()
                        ->scalarNode('attribute')->defaultValue('App\Attribute')->end()
                        ->scalarNode('collection')->defaultValue('App\Collection')->end()
                        ->scalarNode('command')->defaultValue('App\Command')->end()
                        ->scalarNode('configuration')->defaultValue('App\Configuration')->end()
                        ->scalarNode('contracts')->defaultValue('App\Contracts')->end()
                        ->scalarNode('controller')->defaultValue('App\Controller')->end()
                        ->scalarNode('dispatcher')->defaultValue('App\Dispatcher')->end()
                        ->scalarNode('dto')->defaultValue('App\DTO')->end()
                        ->scalarNode('entity')->defaultValue('App\Entity')->end()
                        ->scalarNode('event')->defaultValue('App\Event')->end()
                        ->scalarNode('event_listener')->defaultValue('App\EventListener')->end()
                        ->scalarNode('exception')->defaultValue('App\Exception')->end()
                        ->scalarNode('factory')->defaultValue('App\Factory')->end()
                        ->scalarNode('helper')->defaultValue('App\Helper')->end()
                        ->scalarNode('logger')->defaultValue('App\Logger')->end()
                        ->scalarNode('manager')->defaultValue('App\Manager')->end()
                        ->scalarNode('service_command')->defaultValue('App\Service\Command')->end()
                        ->scalarNode('service_query')->defaultValue('App\Service\Query')->end()
                        ->scalarNode('trait')->defaultValue('App\Trait')->end()
                        ->scalarNode('type')->defaultValue('App\Type')->end()
                        ->scalarNode('vo')->defaultValue('App\VO')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addDirectoriesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('directories')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity')->defaultValue('%kernel.project_dir%/src/Entity')->end()
                        ->scalarNode('form')->defaultValue('%kernel.project_dir%/src/Form')->end()
                        ->scalarNode('vo')->defaultValue('%kernel.project_dir%/src/VO')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addResourcesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('primitives_mapping')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('boolean')->defaultValue('boolean')->end()
                                        ->scalarNode('integer')->defaultValue('int')->end()
                                        ->scalarNode('float')->defaultValue('float')->end()
                                        ->scalarNode('string')->defaultValue('string')->end()
                                        ->scalarNode('array')->defaultValue('array')->end()
                                        ->scalarNode('datetime')->defaultValue('\DateTimeInterface')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('resources')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->defaultValue([
                                        '%kernel.project_dir%/src/Entity',
                                        '%kernel.project_dir%/src/DTO',
                                        '%kernel.project_dir%/src/VO/Entity',
                                    ])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('exclude')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('dto')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('primitives_mapping')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('boolean')->defaultValue('boolean')->end()
                                        ->scalarNode('integer')->defaultValue('int')->end()
                                        ->scalarNode('float')->defaultValue('float')->end()
                                        ->scalarNode('string')->defaultValue('string')->end()
                                        ->scalarNode('array')->defaultValue('array')->end()
                                        ->scalarNode('datetime')->defaultValue('\DateTimeInterface')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('resources')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->defaultValue([
                                        '%kernel.project_dir%/src/DTO',
                                        '%kernel.project_dir%/src/Types',
                                    ])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('exclude')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('value_object')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('primitives_mapping')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('boolean')->defaultValue('boolean')->end()
                                        ->scalarNode('integer')->defaultValue('int')->end()
                                        ->scalarNode('float')->defaultValue('float')->end()
                                        ->scalarNode('string')->defaultValue('string')->end()
                                        ->scalarNode('array')->defaultValue('array')->end()
                                        ->scalarNode('datetime')->defaultValue('\DateTimeInterface')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('resources')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->defaultValue([
                                        '%kernel.project_dir%/src/Collection',
                                        '%kernel.project_dir%/src/Entity',
                                        '%kernel.project_dir%/src/Types',
                                        '%kernel.project_dir%/src/VO',
                                    ])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('exclude')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('service')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('resources')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->defaultValue([
                                        '%kernel.project_dir%/src/VO',
                                    ])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('exclude')
                                    ->beforeNormalization()->castToArray()->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
