<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;

class InterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Contracts',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
        ;

        return $fileDefinition;
    }
}
