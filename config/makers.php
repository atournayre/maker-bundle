<?php
declare(strict_types=1);

use Atournayre\Bundle\MakerBundle\Maker\MakeDto;
use Atournayre\Bundle\MakerBundle\Maker\MakeException;
use Atournayre\Bundle\MakerBundle\Maker\MakeInterface;
use Atournayre\Bundle\MakerBundle\Maker\MakeInterface2;
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
        ->autowire();

    $services
        ->set(MakeDto::class)->tag('maker.command')
        ->set(MakeException::class)->autowire()->tag('maker.command')
        ->set(MakeInterface::class)->tag('maker.command')
        ->set(MakeInterface2::class)->autowire()->tag('maker.command')
        ->set(MakeLogger::class)->autowire()->tag('maker.command')
        ->set(MakeProjectInstall::class)->tag('maker.command')
        ->set(MakeService::class)->tag('maker.command')
        ->set(MakeTrait::class)->tag('maker.command')
        ->set(MakeVo::class)->tag('maker.command')
    ;
};
