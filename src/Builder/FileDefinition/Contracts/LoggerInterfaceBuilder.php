<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;
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

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addExtend(LoggerInterface::class)
            ->addMember(self::methodWithMessage('emergency'))
            ->addMember(self::methodWithMessage('alert'))
            ->addMember(self::methodWithMessage('critical'))
            ->addMember(self::methodWithMessage('error'))
            ->addMember(self::methodWithMessage('warning'))
            ->addMember(self::methodWithMessage('notice'))
            ->addMember(self::methodWithMessage('info'))
            ->addMember(self::methodWithMessage('debug'))
            ->addMember(self::methodLog())
            ->addMember(self::methodException())
            ->addMember(self::methodWithoutMessage('start'))
            ->addMember(self::methodWithoutMessage('end'))
            ->addMember(self::methodWithoutMessage('success'))
            ->addMember(self::methodWithoutMessage('failFast'))
        ;

        return $fileDefinition;
    }

    private static function methodWithMessage(string $methodName): Method
    {
        $method = new Method($methodName);
        $method->setPublic();
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function methodLog(): Method
    {
        $method = new Method('log');
        $method->setPublic();
        $method->addParameter('level');
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function methodException(): Method
    {
        $method = new Method('exception');
        $method->setPublic();
        $method->addParameter('exception')->setType('\Exception');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function methodWithoutMessage(string $methodName): Method
    {
        $method = new Method($methodName);
        $method->setPublic();
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }
}
