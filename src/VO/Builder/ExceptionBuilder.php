<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;

class ExceptionBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();
        $exceptionType = $config->getExtraProperty('exceptionType');

        return (new self($fileDefinition))
            ->createFile()
            ->extends($exceptionType)
            ->addMember(self::namedConstructor($fileDefinition))
            ;
    }

    private static function namedConstructor(FileDefinition $fileDefinition): ?Method
    {
        $config = $fileDefinition->configuration();

        if (!$config->hasExtraProperty('exceptionNamedConstructor')) {
            return null;
        }

        $fullName = $fileDefinition->fullName();
        $className = $fileDefinition->classname();

        $methodName = $config->getExtraProperty('exceptionNamedConstructor');

        return (new Method($methodName))
            ->setStatic()
            ->setPublic()
            ->setReturnType($fullName)
            ->addBody("return new {$className}('Oops, an error occured.');");
    }
}
