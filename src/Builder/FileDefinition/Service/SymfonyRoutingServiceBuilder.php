<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Contracts\Security\UserInterface;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

class SymfonyRoutingServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Routing',
        string      $name = 'SymfonyRoutingService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addImplement(\App\Contracts\Routing\RoutingInterface::class)
            ->addMember(self::addConstruct())
            ->addMember(self::generate())
        ;

        $class->getNamespace()
            ->addUse(\Symfony\Component\Routing\RouterInterface::class)
            ->addUse(\App\Contracts\Routing\RoutingInterface::class)
        ;

        return $fileDefinition;
    }

    private static function addConstruct(): Method
    {
        $method = new Method('__construct');
        $method->setPublic();

        $method
            ->addPromotedParameter('router')
            ->setPrivate()
            ->setType(\Symfony\Component\Routing\RouterInterface::class);
        return $method;
    }

    private static function generate(): Method
    {
        $method = new Method('generate');
        $method->setReturnType('string');
        $method->addParameter('name') ->setType('string');
        $method->addParameter('parameters') ->setType('array')->setDefaultValue([]);

        $method
            ->addParameter('referenceType')
            ->setType('int')
            ->setDefaultValue(new Literal('RoutingInterface::ABSOLUTE_PATH'));

        $method->addBody('return $this->router->generate($name, $parameters, $referenceType);');
        return $method;
    }
}
