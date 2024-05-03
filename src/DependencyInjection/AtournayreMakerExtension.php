<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class AtournayreMakerExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/../config'));
        $loader->load('services.php');
        $loader->load('makers.php');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('atournayre_maker.root_namespace', $config['root_namespace']);

        $container->getDefinition(BundleConfiguration::class)
            ->setArgument(0, [
                'root_namespace' => $config['root_namespace'],
                'namespaces' => $config['namespaces'],
                'resources' => $config['resources'],
            ]);
    }
}
