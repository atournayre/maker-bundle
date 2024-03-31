<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\BuilderHelper;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class SecurityInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = '',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $namespace = 'Contracts\\Security';
        $name = 'Security';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addMember(self::addMethodGetUser())
        ;

        $interface->getNamespace()
            ->addUse(\App\Contracts\Security\UserInterface::class);

        return $fileDefinition;
    }

    private static function addMethodGetUser(): Method
    {
        $method = new Method('getUser');
        $method->setPublic()->setReturnType(\App\Contracts\Security\UserInterface::class)->setReturnNullable();
        return $method;
    }
}
