<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Contracts\Security\UserInterface;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;

class SymfonyRoutingServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Routing',
        string      $name = 'SymfonyRoutingService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();
        $class->addImplement(\App\Contracts\Routing\RoutingInterface::class);

        $class->getNamespace()
            ->addUse(\Symfony\Component\Routing\RouterInterface::class)
            ->addUse(\App\Contracts\Routing\RoutingInterface::class)
        ;

        self::addConstruct($class);
        self::generate($class);

        return $fileDefinition;
    }

    private static function addConstruct(ClassType $class): void
    {
        $class->addMethod('__construct')
            ->setPublic();

        $class->getMethod('__construct')
            ->addPromotedParameter('router')
            ->setPrivate()
            ->setType(\Symfony\Component\Routing\RouterInterface::class);
    }

    private static function generate(ClassType $class): void
    {
        $class->addMethod('generate')
            ->setReturnType('string');

        $class->getMethod('generate')
            ->addParameter('name')
            ->setType('string');

        $class->getMethod('generate')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('generate')
            ->addParameter('referenceType')
            ->setType('int')
            ->setDefaultValue(new Literal('RoutingInterface::ABSOLUTE_PATH'));

        $class->getMethod('generate')
            ->addBody('return $this->router->generate($name, $parameters, $referenceType);');
    }
}
