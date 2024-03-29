<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\InterfaceType;

class ResponseInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Response',
        string      $name = 'ResponseInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        self::addMethodRedirectToUrl($interface);
        self::addMethodRedirectToRoute($interface);
        self::addMethodRender($interface);
        self::addMethodJson($interface);
        self::addMethodJsonError($interface);
        self::addMethodFile($interface);
        self::addMethodEmpty($interface);
        self::addMethodError($interface);

        return $fileDefinition;
    }

    private static function addMethodRedirectToUrl(InterfaceType $interface): void
    {
        $interface->addMethod('redirectToUrl')
            ->setPublic()
            ->addParameter('url')
            ->setType('string');
    }

    private static function addMethodRedirectToRoute(InterfaceType $interface): void
    {
        $interface->addMethod('redirectToRoute')
            ->setPublic()
            ->addParameter('route')
            ->setType('string');

        $interface->getMethod('redirectToRoute')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);
    }

    private static function addMethodRender(InterfaceType $interface): void
    {
        $interface->addMethod('render')
            ->setPublic()
            ->addParameter('view')
            ->setType('string');

        $interface->getMethod('render')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);
    }

    private static function addMethodJson(InterfaceType $interface): void
    {
        $interface->addMethod('json')
            ->setPublic()
            ->addParameter('data')
            ->setType('array');

        $interface->getMethod('json')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(200);

        $interface->getMethod('json')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('json')
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);
    }

    private static function addMethodJsonError(InterfaceType $interface): void
    {
        $interface->addMethod('jsonError')
            ->setPublic()
            ->addParameter('data')
            ->setType('array');

        $interface->getMethod('jsonError')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(400);

        $interface->getMethod('jsonError')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('jsonError')
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);
    }

    private static function addMethodFile(InterfaceType $interface): void
    {
        $interface->addMethod('file')
            ->setPublic()
            ->addParameter('file')
            ->setType('string');

        $interface->getMethod('file')
            ->addParameter('filename')
            ->setType('string');

        $interface->getMethod('file')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);
    }

    private static function addMethodEmpty(InterfaceType $interface): void
    {
        $interface->addMethod('empty')
            ->setPublic()
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(204);

        $interface->getMethod('empty')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);
    }

    private static function addMethodError(InterfaceType $interface): void
    {
        $interface->addMethod('error')
            ->setPublic()
            ->addParameter('view')
            ->setType('string');

        $interface->getMethod('error')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $interface->getMethod('error')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(500);
    }
}
