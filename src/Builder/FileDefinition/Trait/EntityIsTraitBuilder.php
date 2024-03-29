<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
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

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        $namespace = $trait->getNamespace();
        $namespace->addUse(Assert::class);

        $trait->addMethod('is')
            ->addParameter('entity')
            ->setType('self');

        $trait->getMethod('is')
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($entity, \'id\', \'Entity must have an id property\');')
            ->addBody('return $this->id === $entity->id;');

        $trait->addMethod('isNot')
            ->addParameter('entity')
            ->setType('self');

        $trait->getMethod('isNot')
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($entity, \'id\', \'Entity must have an id property\');')
            ->addBody('return $this->id !== $entity->id;');

        return $fileDefinition;
    }
}
