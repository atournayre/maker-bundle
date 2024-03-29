<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Psr\Log\LoggerInterface;

class AbstractLoggerBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = '',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $namespace = 'Logger';
        $name = 'AbstractLogger';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName())
            ->setAbstract();

        $namespace = $class->getNamespace();
        $namespace->addUse(LoggerInterface::class);

        $class->addImplement(LoggerInterface::class);

        self::addProperties($class);

        self::addConstruct($class);
        self::addSetters($class);
        self::addGetters($class);
        self::addPrefixMessageMethod($class);

        self::addMethodsWithMessage($class);
        self::addLogMethod($class);
        self::addExceptionMethod($class);
        self::addMethodsWithoutMessage($class);

        return $fileDefinition;
    }

    private static function addMethodsWithMessage(ClassType $class): void
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
            $class->addMethod($method)
                ->setPublic()
                ->setAbstract()
                ->addParameter('message')
                ->setType('\Stringable|string');

            $class->getMethod($method)
                ->addParameter('context')
                ->setType('array')
                ->setDefaultValue([]);

            $class->getMethod($method)
                ->setReturnType('void');
        }
    }

    private static function addLogMethod(ClassType $class): void
    {
        $class->addMethod('log')
            ->setPublic()
            ->setAbstract()
            ->addParameter('level');

        $class->getMethod('log')
            ->addParameter('message')
            ->setType('\Stringable|string');

        $class->getMethod('log')
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('log')
            ->setReturnType('void');
    }

    private static function addExceptionMethod(ClassType $class): void
    {
        $class->addMethod('exception')
            ->setPublic()
            ->setAbstract()
            ->addParameter('exception')
            ->setType('\Exception');

        $class->getMethod('exception')
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('exception')
            ->setReturnType('void');
    }

    private static function addMethodsWithoutMessage(ClassType $class): void
    {
        $methods = [
            'start',
            'end',
            'success',
            'failFast',
        ];

        foreach ($methods as $method) {
            $class->addMethod($method)
                ->setPublic()
                ->setAbstract()
                ->addParameter('context')
                ->setType('array')
                ->setDefaultValue([]);

            $class->getMethod($method)
                ->setReturnType('void');
        }
    }

    private static function addConstruct(ClassType $class): void
    {
        $method = $class->addMethod('__construct');
        $method->addPromotedParameter('logger')
            ->setProtected()
            ->setType(LoggerInterface::class);
    }

    private static function addPrefixMessageMethod(ClassType $class): void
    {
        $class->addMethod('prefixMessage')
            ->setProtected()
            ->addParameter('prefix')
            ->setType('string');

        $class->getMethod('prefixMessage')
            ->addParameter('message')
            ->setType('\Stringable|string');

        $methodBody = <<<'PHP'
return sprintf('[%s] %s', $prefix, $message);
PHP;

        $class->getMethod('prefixMessage')
            ->setBody($methodBody);

        $class->getMethod('prefixMessage')
            ->setReturnType('string');
    }

    private static function addProperties(ClassType $class): void
    {
        $class->addProperty('logIdentifier')
            ->setPrivate()
            ->setType('?string')
            ->setValue(null);
    }

    private static function addSetters(ClassType $class): void
    {
        $class->addMethod('setLoggerIdentifier')
            ->setProtected()
            ->addParameter('identifier')
            ->setType('string');

        $class->getMethod('setLoggerIdentifier')
            ->setReturnType('void')
            ->addBody('$this->logIdentifier = $identifier;');
    }

    private static function addGetters(ClassType $class): void
    {
        $class->addMethod('getLoggerIdentifier')
            ->setProtected()
            ->setReturnType('string')
            ->addBody('return $this->logIdentifier ?? static::class;');
    }
}
