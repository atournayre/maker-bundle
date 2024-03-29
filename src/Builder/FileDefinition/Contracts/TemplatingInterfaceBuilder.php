<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\InterfaceType;

class TemplatingInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Templating',
        string      $name = 'TemplatingInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        self::addMethodRender($interface);

        return $fileDefinition;
    }

    private static function addMethodRender(InterfaceType $interface): void
    {
        $interface->addMethod('render')
            ->setPublic()
            ->setReturnType('string')
            ->addParameter('template')
            ->setType('string');

        $interface->getMethod('render')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);
    }
}
