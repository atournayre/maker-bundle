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
            fn(array $propertyDatas) => self::defineProperty($propertyDatas),
            $traitProperties
        );

        return (new self($fileDefinition))
            ->createFileAsTrait()
            ->withProperties($properties)
            ->addMembers(self::gettersForObject($traitProperties))
            ->addMembers(self::withersForObject($traitProperties));
    }

    private static function gettersForObject(array $traitProperties): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::gett($property['fieldName']);
            $propertyType = self::correspondingTypes()[$property['type']];

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($property['nullable'])
                ->setBody('return $this->' . $property['fieldName'] . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function withersForObject(array $traitProperties): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::property($property['fieldName']);
            $propertyType = self::correspondingTypes()[$property['type']];

            $method = new Method(Str::wither($fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property['fieldName'])
                ->setType($propertyType);

            $method->getParameter($property['fieldName'])
                ->setNullable($property['nullable']);

            $method->addBody('$clone = clone $this;')
                ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
                ->addBody('return $clone;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function defineProperty(array $propertyDatas): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];
        $nullable = $propertyDatas['nullable'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes()),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes())), $type)
        );

        $propertyType = self::correspondingTypes()[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType)->setNullable($nullable);

        if ($nullable) {
            $property->setValue(null);
        }

        return $property;
    }

    private static function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
        ];
    }
}
