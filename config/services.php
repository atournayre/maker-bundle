<?php

use Atournayre\Bundle\MakerBundle\Builder\AddAttributeBuilder;
use Atournayre\Bundle\MakerBundle\Builder\AddEventsToEntityBuilder;
use Atournayre\Bundle\MakerBundle\Builder\CollectionBuilder;
use Atournayre\Bundle\MakerBundle\Builder\CommandBuilder;
use Atournayre\Bundle\MakerBundle\Builder\ControllerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\DtoBuilder;
use Atournayre\Bundle\MakerBundle\Builder\EnumBuilder;
use Atournayre\Bundle\MakerBundle\Builder\EventBuilder;
use Atournayre\Bundle\MakerBundle\Builder\ExceptionBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FromTemplateBuilder;
use Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\ListenerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\LoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\Builder\ServiceQueryBuilder;
use Atournayre\Bundle\MakerBundle\Builder\TraitForEntityBuilder;
use Atournayre\Bundle\MakerBundle\Builder\TraitForObjectBuilder;
use Atournayre\Bundle\MakerBundle\Builder\VoForEntityBuilder;
use Atournayre\Bundle\MakerBundle\Builder\VoForObjectBuilder;
use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\Service\FilesystemService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

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
        ->alias(Generator::class, 'maker.generator')
        ->set(FilesystemService::class)
    ;

    $services
        ->set(FileGenerator::class)
        ->args([
            '$builders' => tagged_iterator('atournayre_maker.php_file_builder'),
        ])
    ;

    $services->set(BundleConfiguration::class)
        ->factory([BundleConfiguration::class, 'fromArray'])
        ->args([
            abstract_arg('Configuration'),
        ])
    ;

    $builders = [
        AddAttributeBuilder::class,
        AddEventsToEntityBuilder::class,
        CollectionBuilder::class,
        CommandBuilder::class,
        ControllerBuilder::class,
        DtoBuilder::class,
        EnumBuilder::class,
        EventBuilder::class,
        ExceptionBuilder::class,
        FromTemplateBuilder::class,
        InterfaceBuilder::class,
        ListenerBuilder::class,
        LoggerBuilder::class,
        ServiceCommandBuilder::class,
        ServiceQueryBuilder::class,
        TraitForEntityBuilder::class,
        TraitForObjectBuilder::class,
        VoForEntityBuilder::class,
        VoForObjectBuilder::class,
    ];

    foreach ($builders as $builder) {
        $services
            ->set($builder)
            ->tag('atournayre_maker.php_file_builder')
        ;
    }
};
