<?php

use Atournayre\Bundle\MakerBundle\Config\MakerBundleConfig;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
        ->set(MakerBundleConfig::class)
    ;
};
