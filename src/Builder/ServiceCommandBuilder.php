<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Contracts\VO\ContextInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\Config\ServiceCommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

final class ServiceCommandBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ServiceCommandMakerConfiguration::class;
    }

    /**
     * @param ServiceCommandMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $voParameter = $makerConfiguration->vo();

        $attributes = [
            new Attribute(AutoconfigureTag::class, [
                new Literal('TagCommandServiceInterface::class'),
            ]),
        ];

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setReadonly()
            ->setUses([
                FailFast::class,
                AutoconfigureTag::class,
                TagCommandServiceInterface::class,
                $voParameter,
                CommandServiceInterface::class,
                PreConditionsChecksInterface::class,
                FailFastInterface::class,
                TagCommandServiceInterface::class,
                PostConditionsChecksInterface::class,
                ContextInterface::class,
            ])
            ->setAttributes($attributes)
            ->setImplements([
                CommandServiceInterface::class,
                PreConditionsChecksInterface::class,
                FailFastInterface::class,
                PostConditionsChecksInterface::class,
            ])
            ->setMethods([
                ...$this->implementationOfInterface(CommandServiceInterface::class, $voParameter),
                ...$this->implementationOfInterface(PreConditionsChecksInterface::class, $voParameter),
                ...$this->implementationOfInterface(FailFastInterface::class, $voParameter),
                ...$this->implementationOfInterface(PostConditionsChecksInterface::class, $voParameter),
                $this->invoke(),
            ])
        ;
    }

    private function invoke(): Method
    {
        return (new Method('__invoke'))
            ->setPublic()
            ->addComment('This service is not meant to be used directly')
            ->addComment('@throws \RuntimeException')
            ->setBody('throw new \RuntimeException(\'This service is not meant to be used directly\');');
    }

    /**
     * @return array<Method>
     */
    private function implementationOfInterface(string $interface, string $objectType): array
    {
        /** @var InterfaceType $classLike */
        $classLike = InterfaceType::from($interface);

        foreach ($classLike->getMethods() as $method) {
            $methods[] = $this->implementMethod($method->getName(), $classLike, $objectType);
        }

        return $methods ?? [];
    }

    private function implementMethod(string $method, InterfaceType $interfaceType, string $objectType): Method
    {
        $sourceMethod = $interfaceType->getMethod($method);
        return (new Method($sourceMethod->getName()))
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addComment('@param '.Str::classNameFromNamespace($objectType, '').' $object')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }
}
