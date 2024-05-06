<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\ExceptionMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class ExceptionBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ExceptionMakerConfiguration::class;
    }

    public function createInstance(MakerConfigurationInterface|ExceptionMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        return parent::createInstance($makerConfiguration)
            ->setExtends($makerConfiguration->type())
            ->setMethods([
                $this->namedConstructor($makerConfiguration),
            ])
        ;
    }

    private function namedConstructor(MakerConfigurationInterface|ExceptionMakerConfiguration $makerConfiguration): ?Method
    {
        if (!$makerConfiguration->hasNamedConstructor()) {
            return null;
        }

        $methodName = $makerConfiguration->namedConstructor();
        $className = $makerConfiguration->classname();

        return (new Method($methodName))
            ->setStatic()
            ->setPublic()
            ->setReturnType($className)
            ->addBody("return new {$className}('Oops, an error occured.');");
    }
}
