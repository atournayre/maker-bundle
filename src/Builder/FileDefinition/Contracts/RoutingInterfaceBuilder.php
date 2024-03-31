<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\BuilderHelper;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Constant;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

class RoutingInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Routing',
        string      $name = 'RoutingInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addMember(self::addConstantAbsoluteUrl())
            ->addMember(self::addConstantAbsolutePath())
            ->addMember(self::addConstantRelativePath())
            ->addMember(self::addConstantNetworkPath())
            ->addMember(self::addMethodGenerate())
        ;

        return $fileDefinition;
    }

    private static function addConstantAbsoluteUrl(): Constant
    {
        $constant = new Constant('ABSOLUTE_URL');
        $constant->setValue(0);
        $constant->setPublic();
        $constant->addComment('Generates an absolute URL, e.g. "http://example.com/dir/file".');
        return $constant;
    }

    private static function addConstantAbsolutePath(): Constant
    {
        $constant = new Constant('ABSOLUTE_PATH');
        $constant->setValue(1);
        $constant->setPublic();
        $constant->addComment('Generates an absolute path, e.g. "/dir/file".');
        return $constant;
    }

    private static function addConstantRelativePath(): Constant
    {
        $constant = new Constant('RELATIVE_PATH');
        $constant->setValue(2);
        $constant->setPublic();
        $constant->addComment('Generates a relative path based on the current request path, e.g. "../parent-file".');
        return $constant;
    }

    private static function addConstantNetworkPath(): Constant
    {
        $constant = new Constant('NETWORK_PATH');
        $constant->setValue(3);
        $constant->setPublic();
        $constant->addComment('Generates a network path, e.g. "//example.com/dir/file".');
        $constant->addComment('Such reference reuses the current scheme but specifies the host.');
        return $constant;
    }

    private static function addMethodGenerate(): Method
    {
        $method = new Method('generate');
        $method->setPublic()->setReturnType('string');
        $method->addParameter('name')->setType('string');
        $method->addParameter('parameters')->setType('array')->setDefaultValue([]);
        $method->addParameter('referenceType')->setType('int')->setDefaultValue(new Literal('self::ABSOLUTE_PATH'));
        return $method;
    }
}
