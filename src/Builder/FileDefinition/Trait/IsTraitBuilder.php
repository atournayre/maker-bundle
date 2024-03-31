<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;
use Webmozart\Assert\Assert;

class IsTraitBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Trait',
        string $name = 'Is'
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

    private static function addMethodIs(): Method
    {
        $method = new Method('is');
        $method->setPublic();
        $method->addParameter('object')->setType('self');
        $method->setReturnType('bool');
        $method->addBody('Assert::propertyExists($object, \'id\', \'Object must have an id property\');');
        $method->addBody('return $this === $object;');
        return $method;
    }

    private static function addMethodIsNot(): Method
    {
        $method = new Method('isNot');
        $method->setPublic();
        $method->addParameter('object')->setType('self');
        $method->setReturnType('bool');
        $method->addBody('Assert::propertyExists($object, \'id\', \'Object must have an id property\');');
        $method->addBody('return $this !== $object;');
        return $method;
    }
}
