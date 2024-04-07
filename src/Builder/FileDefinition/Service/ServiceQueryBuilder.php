<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Override;
use function Symfony\Component\String\u;

class ServiceQueryBuilder implements FileDefinitionBuilderInterface
{
    #[Override] public static function build(
        MakerConfig $config,
        string $namespace = 'Service\\Query',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $voParameter = $config->rootNamespace() . '\\' . $config->extraProperties()['vo'];
        $voClassName = u($voParameter)->afterLast('\\')->toString();

        $interfacesToImplement = [
            PreConditionsChecksInterface::class,
            FailFastInterface::class,
            TagQueryServiceInterface::class,
            PostConditionsChecksInterface::class,
        ];

        $uses = array_merge($interfacesToImplement, [
            FailFast::class,
            \Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class,
            \App\Contracts\Service\TagQueryServiceInterface::class,
            $voParameter,
        ]);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'QueryService', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->setAttributes([
                self::attributeAutoconfigureTag(),
            ])
            ->addMember(self::invoke())
        ;

        $namespace = $class->getNamespace();

        foreach ($uses as $use) {
            $namespace->addUse($use);
        }

        foreach ($interfacesToImplement as $interface) {
            $class->addImplement($interface);
            $sourceInterface = InterfaceType::from($interface);
            self::implementMethods($sourceInterface, $class, $voClassName);
        }

        return $fileDefinition;
    }

    private static function invoke(): Method
    {
        return (new Method('__invoke'))
            ->setPrivate()
            ->addComment('This service is not meant to be used directly');
    }

    private static function attributeAutoconfigureTag(): Attribute
    {
        return new Attribute(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class, [
            new Literal('TagQueryServiceInterface::class'),
        ]);
    }

    private static function implementMethods($sourceInterface, ClassType $class, string $objectType): void
    {
        foreach ($sourceInterface->getMethods() as $method) {
            $class->addMember(self::implementMethod($method->getName(), $sourceInterface, $objectType));
        }
    }

    private static function implementMethod(string $method, $sourceInterface, string $objectType): Method
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        return (new Method($sourceMethod->getName()))
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addComment('@param '.$objectType.' $object')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }

    public function generateSourceCode(FileDefinition $fileDefinition): string
    {
        throw new \LogicException('Not implemented');
    }
}
