<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Templating\TemplatingInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class TwigTemplatingServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Templating',
        string      $name = 'TwigTemplatingService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addImplement(TemplatingInterface::class)
            ->addMember(self::addConstruct())
            ->addMember(self::addMethodRender())
        ;

        $class->getNamespace()
            ->addUse('Twig\Environment')
            ->addUse(TemplatingInterface::class)
        ;

        return $fileDefinition;
    }

    private static function addConstruct(): Method
    {
        $method = new Method('__construct');
        $method
            ->addPromotedParameter('twig')
            ->setPrivate()
            ->setType('Twig\Environment');
        return $method;
    }

    private static function addMethodRender(): Method
    {
        $method = new Method('render');
        $method
            ->setPublic()
            ->setReturnType('string')
            ->addParameter('template')
            ->setType('string');

        $method
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $method->setBody('return $this->twig->render($template, $parameters);');
        return $method;
    }
}
