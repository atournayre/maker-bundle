<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages the bundle configuration.
 */
final class ElegantMakerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('elegant_maker.namespace_prefix', $config['namespace_prefix']);
        $container->setParameter('elegant_maker.dir_prefix', $config['dir_prefix']);

        // Exception maker configuration
        $container->setParameter('elegant_maker.exception.root_namespace', $config['exception']['root_namespace']);
        $container->setParameter('elegant_maker.exception.target_directory', $config['exception']['target_directory']);

        // Controller maker configuration
        $container->setParameter('elegant_maker.controller.root_namespace', $config['controller']['root_namespace']);
        $container->setParameter('elegant_maker.controller.target_directory', $config['controller']['target_directory']);
        $container->setParameter('elegant_maker.controller.template_path', $config['controller']['template_path']);
        $container->setParameter('elegant_maker.controller.interfaces', $config['controller']['interfaces'] ?? []);

        // Collection maker configuration
        $container->setParameter('elegant_maker.collection.root_namespace', $config['collection']['root_namespace']);
        $container->setParameter('elegant_maker.collection.target_directory', $config['collection']['target_directory']);
        $container->setParameter('elegant_maker.collection.allowed_namespaces', $config['collection']['allowed_namespaces'] ?? []);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
