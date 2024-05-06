<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

class ServiceQueryBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        $config = $fileDefinition->configuration();
        $voParameter = $config->getExtraPropertyWithRootNamespace('vo');

        $attributes = [
            new Attribute(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class, [
                new Literal('TagQueryServiceInterface::class'),
            ]),
        ];

        return static::create($fileDefinition)
            ->createFile()
            ->isReadOnly()
            ->withUse(FailFast::class)
            ->withUse(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class)
            ->withUse(\App\Contracts\Service\TagQueryServiceInterface::class)
            ->withUse($voParameter)
            ->withUse(PreConditionsChecksInterface::class)
            ->withUse(FailFastInterface::class)
            ->withUse(TagQueryServiceInterface::class)
            ->withUse(PostConditionsChecksInterface::class)
            ->withUse(\App\VO\Context::class)
            ->setAttributes($attributes)
            ->addImplement(PreConditionsChecksInterface::class)
            ->addImplement(FailFastInterface::class)
            ->addImplement(PostConditionsChecksInterface::class)
            ->addMember(self::implementationOfInterface(PreConditionsChecksInterface::class, $voParameter))
            ->addMember(self::implementationOfInterface(FailFastInterface::class, $voParameter))
            ->addMember(self::implementationOfInterface(PostConditionsChecksInterface::class, $voParameter))
            ->addMember(self::invoke())
        ;
    }

    private static function invoke(): Method
    {
        return (new Method('__invoke'))
            ->setPublic()
            ->addComment('This service is not meant to be used directly')
            ->addComment('@throws \RuntimeException')
            ->setBody('throw new \RuntimeException(\'This service is not meant to be used directly\');');
    }

    /**
     * @param string $interface
     * @param string $objectType
     * @return array<Method>
     */
    private static function implementationOfInterface(string $interface, string $objectType): array
    {
        /** @var InterfaceType $sourceInterface */
        $sourceInterface = InterfaceType::from($interface);

        foreach ($sourceInterface->getMethods() as $method) {
            $methods[] = self::implementMethod($method->getName(), $sourceInterface, $objectType);
        }

        return $methods ?? [];
    }

    private static function implementMethod(string $method, InterfaceType $sourceInterface, string $objectType): Method
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        return (new Method($sourceMethod->getName()))
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addComment('@param '.Str::classNameFromNamespace($objectType, '').' $object')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }
}
