<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;
use Webmozart\Assert\Assert;

class EntityIsTraitBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Trait',
        string $name = 'EntityIs'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Trait', $config);

        $trait = $fileDefinition
            ->file
            ->addTrait($fileDefinition->fullName())
            ->addMember(self::addMethodIs())
            ->addMember(self::addMethodIsNot())
        ;

        $trait->getNamespace()
            ->addUse(Assert::class)
        ;

        return $fileDefinition;
    }

    public static function addMethodIs(): Method
    {
        $method = new Method('is');
        $method->setPublic()->addParameter('entity')->setType('self');

        $method
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($entity, \'id\', \'Entity must have an id property\');')
            ->addBody('return $this->id === $entity->id;');
        return $method;
    }

    public static function addMethodIsNot(): Method
    {
        $method = new Method('isNot');
        $method->setPublic()->addParameter('entity')->setType('self');

        $method
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($entity, \'id\', \'Entity must have an id property\');')
            ->addBody('return $this->id !== $entity->id;');
        return $method;
    }
}
