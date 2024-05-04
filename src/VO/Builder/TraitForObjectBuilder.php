<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class TraitForObjectBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $traitProperties = $fileDefinition->configuration()->traitProperties();

        $properties = array_map(
            fn(array $propertyDatas) => self::defineProperty($propertyDatas, $fileDefinition),
            $traitProperties
        );

        return (new self($fileDefinition))
            ->createFileAsTrait()
            ->withProperties($properties)
            ->addMembers(self::gettersForObject($traitProperties, $fileDefinition))
            ->addMembers(self::withersForObject($traitProperties, $fileDefinition));
    }

    private static function gettersForObject(array $traitProperties, FileDefinition $fileDefinition): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::getter($property['fieldName']);
            $propertyType = self::correspondingTypes($fileDefinition)[$property['type']];

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($property['nullable'])
                ->setBody('return $this->' . $property['fieldName'] . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function withersForObject(array $traitProperties, FileDefinition $fileDefinition): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::property($property['fieldName']);
            $propertyType = self::correspondingTypes($fileDefinition)[$property['type']];

            $method = new Method(Str::wither($fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property['fieldName'])
                ->setType($propertyType);

            $method->addBody('$clone = clone $this;')
                ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
                ->addBody('return $clone;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function defineProperty(array $propertyDatas, FileDefinition $fileDefinition): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];
        $nullable = $propertyDatas['nullable'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes($fileDefinition)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes($fileDefinition))), $type)
        );

        $propertyType = self::correspondingTypes($fileDefinition)[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType)->setNullable($nullable);

        if ($nullable) {
            $property->setValue(null);
        }

        return $property;
    }
}
