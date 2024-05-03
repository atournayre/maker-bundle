<?php

use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->private()
        ->autowire()
    ;

    $services
        ->alias(Generator::class, 'maker.generator');

    $services
        ->set(FileGenerator::class)
        ->args([
            '$rootDir' => '%kernel.project_dir%/src',
            '$rootNamespace' => '%atournayre_maker.root_namespace%',
        ])
    ;

    $services->set(BundleConfiguration::class)
        ->factory([BundleConfiguration::class, 'fromArray'])
        ->args([
            abstract_arg('Configuration'),
        ])
    ;
};
