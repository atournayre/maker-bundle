<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

class VONullUserBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'VO',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = 'NullUser';
        $namespace = 'VO\\Null';
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();

        $class->addImplement(\App\Contracts\Security\UserInterface::class);

        $class->addMethod('create')
            ->setStatic()
            ->setReturnType('self')
            ->setBody('return new self();');

        $interfacesToImplement = [
            \App\Contracts\Security\UserInterface::class,
        ];

        foreach ($interfacesToImplement as $interface) {
            $sourceInterface = InterfaceType::from($interface);
            self::implementMethods($sourceInterface, $class);
        }

        return $fileDefinition;
    }

    private static function implementMethods($sourceInterface, ClassType $class): void
    {
        foreach ($sourceInterface->getMethods() as $method) {
            self::implementMethod($method->getName(), $sourceInterface, $class);
        }
    }

    private static function implementMethod(string $method, $sourceInterface, ClassType $class): void
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        $class->addMethod($sourceMethod->getName())
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addBody('// TODO: Implement ' . $sourceMethod->getName() . '() method.')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }
}
