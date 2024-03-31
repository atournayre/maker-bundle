<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Trait\EntityIsTrait;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitUse;

class IdEntityTraitBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Trait',
        string $name = 'IdEntity'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Trait', $config);

        $trait = $fileDefinition
            ->file
            ->addTrait($fileDefinition->fullName())
            ->addMember(self::addIdProperty($config))
            ->addMember(self::addGetIdMethod())
            ->addMember(self::addEntityIsTraitUse())
        ;

        $trait->getNamespace()
            ->addUse(\Doctrine\ORM\Mapping::class, 'ORM')
            ->addUse(\ApiPlatform\Metadata\ApiProperty::class)
        ;

        return $fileDefinition;
    }

    private static function addGetIdMethod(): Method
    {
        $method = new Method('getId');
        $method
            ->setReturnType('int')
            ->setBody('return $this->id;');
        return $method;
    }

    private static function addIdProperty(MakerConfig $config): Property
    {
        $property = new Property('id');
        $property->setPrivate()->setType('int');

        $property
            ->addAttribute(\Doctrine\ORM\Mapping\Id::class)
            ->addAttribute(\Doctrine\ORM\Mapping\Column::class)
            ->addAttribute(\Doctrine\ORM\Mapping\GeneratedValue::class);

        if ($config->isEnableApiPlatform()) {
            $property
                ->addAttribute(\ApiPlatform\Metadata\ApiProperty::class, ['identifier' => false]);
        }

        return $property;
    }

    private static function addEntityIsTraitUse(): TraitUse
    {
        return new TraitUse(EntityIsTrait::class);
    }
}
