<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Templating\TemplatingInterface;
use Nette\PhpGenerator\ClassType;

class TwigTemplatingServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Templating',
        string      $name = 'TwigTemplatingService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();
        $class->addImplement(TemplatingInterface::class);

        $class->getNamespace()
            ->addUse('Twig\Environment')
            ->addUse(TemplatingInterface::class)
        ;

        self::addConstruct($class);
        self::addMethodRender($class);

        return $fileDefinition;
    }

    private static function addConstruct(ClassType $class): void
    {
        $class->addMethod('__construct')
            ->addPromotedParameter('twig')
            ->setPrivate()
            ->setType('Twig\Environment');
    }

    private static function addMethodRender(ClassType $class): void
    {
        $class->addMethod('render')
            ->setPublic()
            ->setReturnType('string')
            ->addParameter('template')
            ->setType('string');

        $class->getMethod('render')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('render')
            ->setBody('return $this->twig->render($template, $parameters);');
    }
}
