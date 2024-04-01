<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Logger\AbstractLogger;
use Nette\PhpGenerator\Method;

class LoggerBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Logger',
        string $name = 'DefaultLogger'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Logger', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setExtends(AbstractLogger::class)
            ->addImplement(LoggerInterface::class)
            ->setFinal()
            ->addMember(self::methodException())
            ->addMember(self::addMethodByName('error'))
            ->addMember(self::addMethodByName('emergency'))
            ->addMember(self::addMethodByName('alert'))
            ->addMember(self::addMethodByName('critical'))
            ->addMember(self::addMethodByName('warning'))
            ->addMember(self::addMethodByName('notice'))
            ->addMember(self::addMethodByName('info'))
            ->addMember(self::addMethodByName('debug'))
            ->addMember(self::methodLog())
            ->addMember(self::addMethodWithInfoLog('start', 'start'))
            ->addMember(self::addMethodWithInfoLog('end', 'end'))
            ->addMember(self::addMethodWithInfoLog('success', 'success'))
            ->addMember(self::addMethodWithInfoLog('failFast', 'fail fast'))
        ;

        $class->getNamespace()
            ->addUse(LoggerInterface::class)
        ;

        return $fileDefinition;
    }

    private static function methodException(): Method
    {
        $method = new Method('exception');
        $method->setPublic();
        $method->addParameter('exception')->setType('\Exception');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = <<<'PHP'
$context['exception'] = $exception;
$this->logger->error($exception->getMessage(), $context);
PHP;

        $method->setBody($methodBody);
        return $method;
    }

    private static function addMethodByName(string $name): Method
    {
        $method = new Method($name);
        $method->setPublic()->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->'.$name.'($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);
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

        $methodBody = '$this->logger->log($level, $this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);
        return $method;
    }

    private static function addMethodWithInfoLog(string $name, string $message): Method
    {
        $method = new Method($name);
        $method->setPublic();
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), \''.$message.'\'), $context);';

        $method->setBody($methodBody);
        return $method;
    }
}
