<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;

class ExceptionBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();
        $exceptionType = $config->getExtraProperty('exceptionType');

        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setExtends($exceptionType);

        return (new self($fileDefinition))
            ->withFile($file)
            ->withNamedConstructor();
    }

    private function withNamedConstructor(): self
    {
        $clone = clone $this;
        $config = $clone->fileDefinition->configuration();

        if (!$config->hasExtraProperty('exceptionNamedConstructor')) {
            return $clone;
        }

        $fullName = $clone->fileDefinition->fullName();

        $classes = $clone->file->getClasses();
        $class = $classes[$fullName];

        $methodName = $config->getExtraProperty('exceptionNamedConstructor');

        $method = new Method($methodName);
        $method->setStatic()->setPublic()->setReturnType($fullName);
        $method->addBody("return new {$class->getName()}('Oops, an error occured.');");

        $class->addMember($method);

        return $clone;
    }
}
