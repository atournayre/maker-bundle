<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class TemplatingInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Templating',
        string      $name = 'TemplatingInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addMember(self::addMethodRender());

        return $fileDefinition;
    }

    private static function addMethodRender(): Method
    {
        $method = new Method('render');
        $method->setPublic()->setReturnType('string');
        $method->addParameter('template')->setType('string');
        $method->addParameter('parameters')->setType('array')->setDefaultValue([]);
        return $method;
    }
}
