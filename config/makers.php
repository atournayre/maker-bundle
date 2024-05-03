<?php
declare(strict_types=1);

use Atournayre\Bundle\MakerBundle\Maker\MakeAddEventsToEntities;
use Atournayre\Bundle\MakerBundle\Maker\MakeCollection;
use Atournayre\Bundle\MakerBundle\Maker\MakeCommand;
use Atournayre\Bundle\MakerBundle\Maker\MakeController;
use Atournayre\Bundle\MakerBundle\Maker\MakeDto;
use Atournayre\Bundle\MakerBundle\Maker\MakeEvent;
use Atournayre\Bundle\MakerBundle\Maker\MakeException;
use Atournayre\Bundle\MakerBundle\Maker\MakeInterface;
use Atournayre\Bundle\MakerBundle\Maker\MakeLogger;
use Atournayre\Bundle\MakerBundle\Maker\MakeProjectInstall;
use Atournayre\Bundle\MakerBundle\Maker\MakeService;
use Atournayre\Bundle\MakerBundle\Maker\MakeTrait;
use Atournayre\Bundle\MakerBundle\Maker\MakeVo;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->private()
        ->autowire()
    ;

    $makers = [
        MakeAddEventsToEntities::class,
        MakeCollection::class,
        MakeCommand::class,
        MakeController::class,
        MakeDto::class,
        MakeEvent::class,
        MakeException::class,
        MakeInterface::class,
        MakeLogger::class,
        MakeProjectInstall::class,
        MakeService::class,
        MakeTrait::class,
        MakeVo::class,
    ];

    foreach ($makers as $maker) {
        $services
            ->set($maker)
            ->arg('$rootDir', '%kernel.project_dir%/src')
            ->tag('maker.command')
        ;
    }
};
