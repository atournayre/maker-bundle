<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;

class RoutingInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Routing',
        string      $name = 'RoutingInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        self::addConstantAbsoluteUrl($interface);
        self::addConstantAbsolutePath($interface);
        self::addConstantRelativePath($interface);
        self::addConstantNetworkPath($interface);
        self::addMethodGenerate($interface);

        return $fileDefinition;
    }

    private static function addConstantAbsoluteUrl(InterfaceType $interface): void
    {
        $interface->addConstant('ABSOLUTE_URL', 0)
            ->setPublic()
            ->addComment('Generates an absolute URL, e.g. "http://example.com/dir/file".');
    }

    private static function addConstantAbsolutePath(InterfaceType $interface): void
    {
        $interface->addConstant('ABSOLUTE_PATH', 1)
            ->setPublic()
            ->addComment('Generates an absolute path, e.g. "/dir/file".');
    }

    private static function addConstantRelativePath(InterfaceType $interface): void
    {
        $interface->addConstant('RELATIVE_PATH', 2)
            ->setPublic()
            ->addComment('Generates a relative path based on the current request path, e.g. "../parent-file".');
    }

    private static function addConstantNetworkPath(InterfaceType $interface): void
    {
        $interface->addConstant('NETWORK_PATH', 3)
            ->setPublic()
            ->addComment('Generates a network path, e.g. "//example.com/dir/file".')
            ->addComment('Such reference reuses the current scheme but specifies the host.');
    }

    private static function addMethodGenerate(InterfaceType $interface): void
    {
        $interface->addMethod('generate')
            ->setPublic()
            ->setReturnType('string')
            ->addParameter('name')
            ->setType('string');

        $interface->getMethod('generate')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('generate')
            ->addParameter('referenceType')
            ->setType('int')
            ->setDefaultValue(new Literal('self::ABSOLUTE_PATH'));
    }
}
