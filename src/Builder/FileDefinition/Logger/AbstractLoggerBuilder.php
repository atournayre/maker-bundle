<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Psr\Log\LoggerInterface;

class AbstractLoggerBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = '',
        string      $name = ''
    ): FileDefinitionBuilder
    {
        $namespace = 'Logger';
        $name = 'AbstractLogger';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setAbstract()
            ->addImplement(LoggerInterface::class)
            ->addMember(self::propertyLogIdentifier())
            ->addMember(self::construct())
            ->addMember(self::setLoggerIdentifier())
            ->addMember(self::getLoggerIdentifier())
            ->addMember(self::methodPrefixMessage())
            ->addMember(self::abstractMethodWithMessage('emergency'))
            ->addMember(self::abstractMethodWithMessage('alert'))
            ->addMember(self::abstractMethodWithMessage('critical'))
            ->addMember(self::abstractMethodWithMessage('error'))
            ->addMember(self::abstractMethodWithMessage('warning'))
            ->addMember(self::abstractMethodWithMessage('notice'))
            ->addMember(self::abstractMethodWithMessage('info'))
            ->addMember(self::abstractMethodWithMessage('debug'))
            ->addMember(self::methodLog())
            ->addMember(self::methodException())
            ->addMember(self::abstractMethodWithoutMessage('start'))
            ->addMember(self::abstractMethodWithoutMessage('end'))
            ->addMember(self::abstractMethodWithoutMessage('success'))
            ->addMember(self::abstractMethodWithoutMessage('failFast'));

        $class->getNamespace()
            ->addUse(LoggerInterface::class);

        return $fileDefinition;
    }

    private static function propertyLogIdentifier(): Property
    {
        $property = new Property('logIdentifier');
        $property->setPrivate();
        $property->setType('?string');
        $property->setValue(null);
        return $property;
    }

    private static function construct(): Method
    {
        $method = new Method('__construct');
        $method->addPromotedParameter('logger')->setProtected()->setType(LoggerInterface::class);
        return $method;
    }

    private static function setLoggerIdentifier(): Method
    {
        $method = new Method('setLoggerIdentifier');
        $method->setProtected()->addParameter('identifier')->setType('string');
        $method->setReturnType('void');
        $method->addBody('$this->logIdentifier = $identifier;');
        return $method;
    }

    private static function getLoggerIdentifier(): Method
    {
        $method = new Method('getLoggerIdentifier');
        $method->setProtected()
            ->setReturnType('string')
            ->addBody('return $this->logIdentifier ?? static::class;');
        return $method;
    }

    private static function methodPrefixMessage(): Method
    {
        $method = new Method('prefixMessage');
        $method->setProtected()->addParameter('prefix')->setType('string');
        $method->addParameter('message')->setType('\Stringable|string');

        $methodBody = <<<'PHP'
return sprintf('[%s] %s', $prefix, $message);
PHP;

        $method->setBody($methodBody);

        $method->setReturnType('string');
        return $method;
    }

    private static function abstractMethodWithMessage(string $methodName): Method
    {
        $method = new Method($methodName);
        $method->setPublic()->setAbstract();
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function methodLog(): Method
    {
        $method = new Method('log');
        $method->setPublic()->setAbstract();
        $method->addParameter('level');
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function methodException(): Method
    {
        $method = new Method('exception');
        $method->setPublic()->setAbstract();
        $method->addParameter('exception')->setType('\Exception');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }

    private static function abstractMethodWithoutMessage(string $methodName): Method
    {
        $method = new Method($methodName);
        $method->setPublic()->setAbstract();
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        return $method;
    }
}
