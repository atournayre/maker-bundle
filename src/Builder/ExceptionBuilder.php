<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\ExceptionMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class ExceptionBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ExceptionMakerConfiguration::class;
    }

    /**
     * @param ExceptionMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setExtends($makerConfiguration->type())
            ->setMethods([
                $this->namedConstructor($makerConfiguration),
            ])
        ;
    }

    private function namedConstructor(ExceptionMakerConfiguration $makerConfiguration): ?Method
    {
        if (!$makerConfiguration->hasNamedConstructor()) {
            return null;
        }

        $methodName = $makerConfiguration->namedConstructor();
        $fqcn = $makerConfiguration->fqcn;
        $className = $makerConfiguration->classname();

        return (new Method($methodName))
            ->setStatic()
            ->setPublic()
            ->setReturnType($fqcn)
            ->addBody("return new {$className}('Oops, an error occured.');");
    }
}
