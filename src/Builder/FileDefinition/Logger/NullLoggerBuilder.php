<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Logger\AbstractLogger;
use Nette\PhpGenerator\ClassType;

class NullLoggerBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Logger',
        string $name = 'NullLogger'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName())
            ->setExtends(AbstractLogger::class)
            ->addImplement(LoggerInterface::class)
            ->setFinal();

        $namespace = $class->getNamespace();
        $namespace->addUse(LoggerInterface::class);

        self::addExceptionMethod($class);
        self::addMethodByName($class, 'error');
        self::addMethodByName($class, 'emergency');
        self::addMethodByName($class, 'alert');
        self::addMethodByName($class, 'critical');
        self::addMethodByName($class, 'warning');
        self::addMethodByName($class, 'notice');
        self::addMethodByName($class, 'info');
        self::addMethodByName($class, 'debug');

        self::addLogMethod($class);

        self::addMethodWithInfoLog($class, 'start', 'start');
        self::addMethodWithInfoLog($class, 'end', 'end');
        self::addMethodWithInfoLog($class, 'success', 'success');
        self::addMethodWithInfoLog($class, 'failFast', 'fail fast');

        return $fileDefinition;
    }

    private static function addExceptionMethod(ClassType $class): void
    {
        $class->addMethod('exception')
            ->setPublic()
            ->addParameter('exception')
            ->setType('\Exception');

        $class->getMethod('exception')
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('exception')
            ->setReturnType('void');

        $methodBody = '// Do nothing';

        $class->getMethod('exception')
            ->setBody($methodBody);
    }

    private static function addMethodByName(ClassType $class, string $name): void
    {
        $class->addMethod($name)
            ->setPublic()
            ->addParameter('message')
            ->setType('\Stringable|string');

        $class->getMethod($name)
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod($name)
            ->setReturnType('void');

        $methodBody = '// Do nothing';

        $class->getMethod($name)
            ->setBody($methodBody);
    }

    private static function addLogMethod(ClassType $class): void
    {
        $class->addMethod('log')
            ->setPublic()
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

        $methodBody = '// Do nothing';

        $class->getMethod('log')
            ->setBody($methodBody);
    }

    private static function addMethodWithInfoLog(ClassType $class, string $name, string $message): void
    {
        $class->addMethod($name)
            ->setPublic()
            ->addParameter('context')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod($name)
            ->setReturnType('void');

        $methodBody = '// Do nothing';

        $class->getMethod($name)
            ->setBody($methodBody);
    }
}
