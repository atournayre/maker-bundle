<?php

use Atournayre\Bundle\MakerBundle\Generator\AbstractGenerator;
use Atournayre\Bundle\MakerBundle\Generator\DtoGenerator;
use Atournayre\Bundle\MakerBundle\Generator\EntityTraitGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ExceptionGenerator;
use Atournayre\Bundle\MakerBundle\Generator\InterfaceGenerator;
use Atournayre\Bundle\MakerBundle\Generator\LoggerGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ProjectInstallGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ServiceCommandGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ServiceQueryGenerator;
use Atournayre\Bundle\MakerBundle\Generator\TraitGenerator;
use Atournayre\Bundle\MakerBundle\Generator\VoGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->parameters()
            ->set('atournayre_maker.root_namespace', 'root_namespace')
    ;

    $services = $container->services()
        ->defaults()
        ->private()
        ->autowire()
    ;

    $abstractGeneratorArguments = [
        param('kernel.project_dir'),
        param('atournayre_maker.root_namespace'),
    ];

    $generators = [
        AbstractGenerator::class,
        DtoGenerator::class,
        EntityTraitGenerator::class,
        ExceptionGenerator::class,
        InterfaceGenerator::class,
        LoggerGenerator::class,
        ProjectInstallGenerator::class,
        ServiceCommandGenerator::class,
        ServiceQueryGenerator::class,
        TraitGenerator::class,
        VoGenerator::class,
    ];

    foreach ($generators as $generator) {
        $services
            ->set($generator)->public()
            ->args($abstractGeneratorArguments);
    }

    $services
        ->alias(Generator::class, 'maker.generator');
};
