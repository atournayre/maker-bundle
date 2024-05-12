<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Aimeos\Map;
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
        $methods = Map::from([
            $this->namedConstructor($makerConfiguration),
        ])->filter();

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setExtends($makerConfiguration->type())
            ->setMethods($methods->toArray())
        ;
    }

    private function namedConstructor(ExceptionMakerConfiguration $exceptionMakerConfiguration): ?Method
    {
        if (!$exceptionMakerConfiguration->hasNamedConstructor()) {
            return null;
        }

        $methodName = $exceptionMakerConfiguration->namedConstructor();
        $fqcn = $exceptionMakerConfiguration->fqcn;
        $className = $exceptionMakerConfiguration->classname();

        return (new Method($methodName))
            ->setStatic()
            ->setPublic()
            ->setReturnType($fqcn)
            ->addBody(sprintf("return new %s('Oops, an error occured.');", $className));
    }
}
