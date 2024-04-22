<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class CollectionBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $extends = self::extendsClass($fileDefinition);
        $type = self::collectionType($fileDefinition);
        $property = self::propertyType($fileDefinition);

        return (new self($fileDefinition))
            ->createFile()
            ->extends($extends)
            ->withUse($extends)
            ->withUse($type)
            ->addMember($property)
        ;
    }

    private static function propertyType(FileDefinition $fileDefinition): Property
    {
        $type = self::collectionType($fileDefinition);

        return (new Property('type'))
            ->setVisibility('protected')
            ->setStatic()
            ->setType('string')
            ->setValue(new Literal(Str::classNameSemiColonFromNamespace($type)))
        ;
    }

    private static function extendsClass(FileDefinition $fileDefinition): string
    {
        $config = $fileDefinition->configuration();

        if ($config->hasExtraProperty('collectionOfDecimals')) {
            return \Atournayre\Collection\DecimalValueCollection::class;
        }

        Assert::true($config->hasExtraProperty('collectionIsImmutable'), 'The collectionIsImmutable property is required');

        return $config->getExtraProperty('collectionIsImmutable')
            ? \Atournayre\Collection\TypedCollectionImmutable::class
            : \Atournayre\Collection\TypedCollection::class;
    }

    private static function collectionType(FileDefinition $fileDefinition): string
    {
        $config = $fileDefinition->configuration();

        if ($config->hasExtraProperty('collectionOfDecimals')) {
            return \Atournayre\Types\DecimalValue::class;
        }

        Assert::true($config->hasExtraProperty('collectionRelatedObject'), 'The collectionRelatedObject property is required');

        return Str::prefixByRootNamespace(
            $config->getExtraProperty('collectionRelatedObject'),
            $config->rootNamespace()
        );
    }
}
