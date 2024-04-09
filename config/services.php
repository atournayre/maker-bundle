<?php

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\FromTemplateBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\TraitBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO\VOBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO\VOForEntityBuilder;
use Atournayre\Bundle\MakerBundle\Generator\AbstractGenerator;
use Atournayre\Bundle\MakerBundle\Generator\EntityTraitGenerator;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ProjectInstallGenerator;
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
            ->set('atournayre_maker.root_namespace', 'App')
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
        EntityTraitGenerator::class,
        ProjectInstallGenerator::class,
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

    $services
        ->set(FileGenerator::class);

    $builders = [
        VOBuilder::class,
        VOForEntityBuilder::class,
        FromTemplateBuilder::class,
        TraitBuilder::class,
    ];

    foreach ($builders as $builder) {
        $services
            ->set($builder)->public()
            ->tag('atournayre_maker.builder')
        ;
    }
};
