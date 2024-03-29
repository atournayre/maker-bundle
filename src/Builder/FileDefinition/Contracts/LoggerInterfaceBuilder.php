<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\InterfaceType;
use Psr\Log\LoggerInterface;

class LoggerInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = '',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $namespace = 'Contracts\\Logger';
        $name = 'Logger';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        $interface->addExtend(LoggerInterface::class);

        self::addMethodsWithMessage($interface);
        self::addLogMethod($interface);
        self::addExceptionMethod($interface);
        self::addMethodsWithoutMessage($interface);

        return $fileDefinition;
    }

    private static function addMethodsWithMessage(InterfaceType $interface): void
    {
        $methods = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        foreach ($methods as $method) {
            $interface->addMethod($method)
                ->setPublic()
                ->addParameter('message')
                ->setType('\Stringable|string');

            $interface->getMethod($method)
                ->addParameter('context')
                ->setType('array')
                ->setDefaultValue([]);

            $interface->getMethod($method)
                ->setReturnType('void');
        }
    }

    private static function addLogMethod(InterfaceType $interface): void
    {
        $interface->addMethod('log')
            ->setPublic()
            ->addParameter('level');

        $interface->getMethod('log')
            ->addParameter('message')
            ->setType('\Stringable|string');

        $interface->getMethod('log')
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('log')
            ->setReturnType('void');
    }

    private static function addExceptionMethod(InterfaceType $interface): void
    {
        $interface->addMethod('exception')
            ->setPublic()
            ->addParameter('exception')
            ->setType('\Exception');

        $interface->getMethod('exception')
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('exception')
            ->setReturnType('void');
    }

    private static function addMethodsWithoutMessage(InterfaceType $interface): void
    {
        $methods = [
            'start',
            'end',
            'success',
            'failFast',
        ];

        foreach ($methods as $method) {
            $interface->addMethod($method)
                ->setPublic()
                ->addParameter('context')
                ->setType('array')
                ->setDefaultValue([]);

            $interface->getMethod($method)
                ->setReturnType('void');
        }
    }
}
