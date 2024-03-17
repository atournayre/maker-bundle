<?php

use Atournayre\Bundle\MakerBundle\Generator\InterfaceGenerator;
use Atournayre\Bundle\MakerBundle\Maker\MakeInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->parameters()
            ->set('atournayre_maker.skeleton_dir', 'skeleton_dir')
            ->set('atournayre_maker.root_dir', 'root_dir')
            ->set('atournayre_maker.root_namespace', 'root_namespace')
    ;

    $services = $container->services()
        ->defaults()->private();

    $services
        ->set(InterfaceGenerator::class)->public()
            ->arg('$projectDir', param('kernel.project_dir'))
            ->arg('$skeletonDir', param('atournayre_maker.skeleton_dir'))
            ->arg('$rootNamespace', param('atournayre_maker.root_namespace'))
            ->arg('$rootDir', param('kernel.project_dir').'/'.param('atournayre_maker.root_dir'));

    $services
        ->set(MakeInterface::class)->public()
            ->arg('$interfaceGenerator', service(InterfaceGenerator::class))
            ->tag('console.command');

    $services
        ->alias(Generator::class, 'maker.generator');
};
