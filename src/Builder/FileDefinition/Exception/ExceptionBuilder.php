<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Exception;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use function Symfony\Component\String\u;

class ExceptionBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Exception',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $exceptionType = $config->getExtraProperty('exceptionType') ?? \Exception::class;

        $class = $fileDefinition->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setExtends($exceptionType)
        ;

        self::addNamedConstructor($class, $config);

        return $fileDefinition;
    }

    private static function cleanName(string $name): string
    {
        if (! u($name)->endsWith('Exception')) {
            return $name;
        }

        return u($name)->replace('Exception', '')->toString();
    }

    private static function addNamedConstructor(ClassType $class, MakerConfig $config): void
    {
        $methodName = $config->getExtraProperty('exceptionNamedConstructor');

        if ('' === $methodName) {
            return;
        }

        $fullName = $class->getNamespace()->getName() . '\\' . $class->getName();

        $class->addMethod($methodName)
            ->setStatic()
            ->setPublic()
            ->setReturnType($fullName);

        $class->getMethod($methodName)
            ->addBody("return new {$class->getName()}('Oops, an error occured.');");
    }

    public static function buildFailFast(
        MakerConfig $config,
        string $namespace = 'Exception',
        string $name = 'FailFast'
    ): FileDefinitionBuilder
    {
        $config = $config
            ->withExtraProperty('exceptionType', \RuntimeException::class)
            ->withExtraProperty('exceptionNamedConstructor', 'ifTrue')
        ;

        return self::build($config, $namespace, $name);
    }
}
