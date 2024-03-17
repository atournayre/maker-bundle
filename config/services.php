<?php

use Atournayre\Bundle\MakerBundle\Generator\InterfaceGenerator;
use Atournayre\Bundle\MakerBundle\Maker\MakeInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

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

    $container
        ->services()
            ->defaults()
                ->autowire()
                ->autoconfigure()
                ->public()
                ->bind('string $projectDir', param('kernel.project_dir'))
                ->bind('string $skeletonDir', param('atournayre_maker.skeleton_dir'))
                ->bind('string $rootNamespace', param('atournayre_maker.root_namespace'))
                ->bind('string $rootDir', param('kernel.project_dir').'/'.param('atournayre_maker.root_dir'))

            ->load('Atournayre\\Bundle\\MakerBundle\\', '../src/*')

            ->set(InterfaceGenerator::class, InterfaceGenerator::class)->public()
            ->set('atournayre_maker.make_interface', MakeInterface::class)->tag('maker.command')->public()

            ->alias(Generator::class, 'maker.generator')
    ;
};
