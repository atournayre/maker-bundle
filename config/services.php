<?php

use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->parameters()
            ->set('atournayre_maker.root_namespace', 'App')
    ;

    $services = $container->services()
        ->defaults()
        ->private()
        ->autowire()
    ;

    $services
        ->alias(Generator::class, 'maker.generator');

    $services
        ->set(FileGenerator::class);
};
