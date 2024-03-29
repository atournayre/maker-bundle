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
            ->set('atournayre_maker.root_namespace', 'root_namespace')
    ;

    $services = $container->services()
        ->defaults()->private();

    $services
        ->set(InterfaceGenerator::class)->public()
            ->arg('$projectDir', param('kernel.project_dir'))
            ->arg('$rootNamespace', param('atournayre_maker.root_namespace'));

    $services
        ->set('atournayre.maker.make_interface', MakeInterface::class)->public()
            ->arg('$interfaceGenerator', service(InterfaceGenerator::class));

    $services
        ->alias(Generator::class, 'maker.generator');

    $services
        ->load('Atournayre\\Bundle\\MakerBundle\\', '../src/*');
};
