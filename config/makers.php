<?php


use Atournayre\Bundle\MakerBundle\Maker\MakeInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()->private();

    $services
        ->set(MakeInterface::class)->tag('maker.command');

};
