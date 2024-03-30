<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Exception\FailFast;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Override;
use function Symfony\Component\String\u;

class ServiceCommandBuilder implements FileDefinitionBuilderInterface
{
    #[Override] public static function build(
        MakerConfig $config,
        string $namespace = 'Service\\Command',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'CommandService', $config);
        $fileDefinition->file->addClass($fileDefinition->fullName());

        $class = $fileDefinition->getClass();
        $class->setFinal()->setReadOnly();

        $namespace = $class->getNamespace();
        $namespace->addUse(FailFast::class);
        $namespace->addUse(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class);
        $namespace->addUse(\App\Contracts\Service\TagCommandServiceInterface::class);

        $class->addAttribute(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class, [
            new Literal('TagCommandServiceInterface::class'),
        ]);

        $class->addMethod('__invoke')
            ->setPrivate()
            ->addComment('This service is not meant to be used directly');

        $interfacesToImplement = [
            PreConditionsChecksInterface::class,
            FailFastInterface::class,
            TagCommandServiceInterface::class,
            PostConditionsChecksInterface::class,
        ];

        foreach ($interfacesToImplement as $interface) {
            $class->addImplement($interface);
            $namespace->addUse($interface);
        }

        $voParameter = $config->rootNamespace() . '\\' . $config->extraProperties()['vo'];
        $voClassName = u($voParameter)->afterLast('\\')->toString();

        $namespace->addUse($voParameter);

        foreach ($interfacesToImplement as $interface) {
            $sourceInterface = InterfaceType::from($interface);
            self::implementMethods($sourceInterface, $class, $voClassName);
        }

        return $fileDefinition;
    }

    private static function implementMethods($sourceInterface, ClassType $class, string $objectType): void
    {
        foreach ($sourceInterface->getMethods() as $method) {
            self::implementMethod($method->getName(), $sourceInterface, $class, $objectType);
        }
    }

    private static function implementMethod(string $method, $sourceInterface, ClassType $class, string $objectType): void
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        $class->addMethod($sourceMethod->getName())
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addComment('@param '.$objectType.' $object')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }
}
