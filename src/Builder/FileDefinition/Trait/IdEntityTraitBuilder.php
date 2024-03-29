<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Trait\EntityIsTrait;
use Nette\PhpGenerator\TraitType;
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

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        self::addIdProperty($trait, $config);
        self::addGetIdMethod($trait);

        $namespace = $trait->getNamespace();
        $namespace->addUse(\Doctrine\ORM\Mapping::class, 'ORM');
        $namespace->addUse(\ApiPlatform\Metadata\ApiProperty::class);

        self::addEntityIsTraitUse($trait);

        return $fileDefinition;
    }

    private static function addGetIdMethod(TraitType $trait): void
    {
        $trait->addMethod('getId')
            ->setReturnType('int')
            ->setBody('return $this->id;');
    }

    private static function addIdProperty(TraitType $trait, MakerConfig $config): void
    {
        $trait->addProperty('id')
            ->setPrivate()
            ->setType('int');

        $trait->getProperty('id')
            ->addAttribute(\Doctrine\ORM\Mapping\Id::class)
            ->addAttribute(\Doctrine\ORM\Mapping\Column::class)
            ->addAttribute(\Doctrine\ORM\Mapping\GeneratedValue::class);

        if ($config->isEnableApiPlatform()) {
            $trait->getProperty('id')
                ->addAttribute(\ApiPlatform\Metadata\ApiProperty::class, ['identifier' => false]);
        }
    }

    private static function addEntityIsTraitUse(TraitType $trait): void
    {
        $traitUse = new TraitUse(EntityIsTrait::class);
        $trait->addMember($traitUse);
    }
}
