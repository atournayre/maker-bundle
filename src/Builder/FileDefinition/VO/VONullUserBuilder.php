<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;

class VONullUserBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'VO',
        string      $name = ''
    ): FileDefinitionBuilder
    {
        $name = 'NullUser';
        $namespace = 'VO\\Null';
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addImplement(\App\Contracts\Security\UserInterface::class)
            ->addMember(self::addMethodCreate());

        self::implementMethods($class);

        return $fileDefinition;
    }

    private static function addMethodCreate(): Method
    {
        $method = new Method('create');
        $method->setStatic();
        $method->setReturnType('self');
        $method->addBody('return new self();');
        return $method;
    }

    private static function implementMethods(ClassType $class): void
    {
        $interfacesToImplement = [
            \App\Contracts\Security\UserInterface::class,
        ];

        foreach ($interfacesToImplement as $interface) {
            $sourceInterface = InterfaceType::from($interface);
            self::implementMethodsFromInterface($sourceInterface, $class);
        }
    }

    private static function implementMethodsFromInterface($sourceInterface, ClassType $class): void
    {
        foreach ($sourceInterface->getMethods() as $method) {
            $class->addMember(self::implementMethod($method->getName(), $sourceInterface));
        }
    }

    private static function implementMethod(string $method, $sourceInterface): Method
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        $method = new Method($sourceMethod->getName());
        $method
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addBody('// TODO: Implement ' . $sourceMethod->getName() . '() method.')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
        return $method;
    }
}
