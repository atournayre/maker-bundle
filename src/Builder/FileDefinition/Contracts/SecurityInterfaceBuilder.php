<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;

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

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        $namespace1 = $interface->getNamespace();
        $namespace1->addUse(\App\Contracts\Security\UserInterface::class);

        $interface->addMethod('getUser')
            ->setPublic()
            ->setReturnType(\App\Contracts\Security\UserInterface::class)
            ->setReturnNullable();

        return $fileDefinition;
    }
}
