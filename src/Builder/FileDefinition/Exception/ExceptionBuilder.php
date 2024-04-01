<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Exception;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
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

        if ('' === $config->getExtraProperty('exceptionNamedConstructor')) {
            $class->addMember(self::methodNamedConstructor($class, $config));
        }


        return $fileDefinition;
    }

    private static function cleanName(string $name): string
    {
        if (! u($name)->endsWith('Exception')) {
            return $name;
        }

        return u($name)->replace('Exception', '')->toString();
    }

    private static function methodNamedConstructor(ClassType $class, MakerConfig $config): Method
    {
        $methodName = $config->getExtraProperty('exceptionNamedConstructor');
        $fullName = $class->getNamespace()->getName() . '\\' . $class->getName();

        $method = new Method($methodName);
        $method->setStatic()->setPublic()->setReturnType($fullName);
        $method->addBody("return new {$class->getName()}('Oops, an error occured.');");
        return $method;
    }
}
