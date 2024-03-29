<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
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

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        $namespace = $trait->getNamespace();
        $namespace->addUse(Assert::class);

        $trait->addMethod('is')
            ->addParameter('object')
            ->setType('self');

        $trait->getMethod('is')
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($object, \'id\', \'Object must have an id property\');')
            ->addBody('return $this === $object;');

        $trait->addMethod('isNot')
            ->addParameter('object')
            ->setType('self');

        $trait->getMethod('isNot')
            ->setReturnType('bool')
            ->addBody('Assert::propertyExists($object, \'id\', \'Object must have an id property\');')
            ->addBody('return $this !== $object;');

        return $fileDefinition;
    }
}
