<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class ResponseInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Contracts\\Response',
        string      $name = 'ResponseInterface'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addMember(self::methodRedirectToUrl())
            ->addMember(self::methodRedirectToRoute())
            ->addMember(self::methodRender())
            ->addMember(self::methodJson())
            ->addMember(self::methodJsonError())
            ->addMember(self::methodFile())
            ->addMember(self::methodEmpty())
            ->addMember(self::methodError())
        ;

        return $fileDefinition;
    }

    private static function methodRedirectToUrl(): Method
    {
        $method = new Method('redirectToUrl');
        $method->setPublic();
        $method->addParameter('url')->setType('string');
        return $method;
    }

    private static function methodRedirectToRoute(): Method
    {
        $method = new Method('redirectToRoute');
        $method->setPublic();
        $method->addParameter('route')->setType('string');
        $method->addParameter('parameters')->setType('array')->setDefaultValue([]);
        return $method;
    }

    private static function methodRender(): Method
    {
        $method = new Method('render');
        $method->setPublic();
        $method->addParameter('view')->setType('string');
        $method->addParameter('parameters')->setType('array')->setDefaultValue([]);
        return $method;
    }

    private static function methodJson(): Method
    {
        $method = new Method('json');
        $method->setPublic();
        $method->addParameter('data')->setType('array');
        $method->addParameter('status')->setType('int')->setDefaultValue(200);
        $method->addParameter('headers')->setType('array')->setDefaultValue([]);
        $method->addParameter('json')->setType('bool')->setDefaultValue(false);
        return $method;
    }

    private static function methodJsonError(): Method
    {
        $method = new Method('jsonError');
        $method->setPublic();
        $method->addParameter('data')->setType('array');
        $method->addParameter('status')->setType('int')->setDefaultValue(400);
        $method->addParameter('headers')->setType('array')->setDefaultValue([]);
        $method->addParameter('json')->setType('bool')->setDefaultValue(false);
        return $method;
    }

    private static function methodFile(): Method
    {
        $method = new Method('file');
        $method->setPublic();
        $method->addParameter('file')->setType('string');
        $method->addParameter('filename')->setType('string');
        $method->addParameter('headers')->setType('array')->setDefaultValue([]);
        return $method;
    }

    private static function methodEmpty(): Method
    {
        $method = new Method('empty');
        $method->setPublic();
        $method->addParameter('status')->setType('int')->setDefaultValue(204);
        $method->addParameter('headers')->setType('array')->setDefaultValue([]);
        return $method;
    }

    private static function methodError(): Method
    {
        $method = new Method('error');
        $method->setPublic();
        $method->addParameter('view')->setType('string');
        $method->addParameter('parameters')->setType('array')->setDefaultValue([]);
        $method->addParameter('status')->setType('int')->setDefaultValue(500);
        return $method;
    }
}
