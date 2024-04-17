<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class TraitForEntityBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();
        $traitProperties = $config->traitProperties();

        $uses = [
            \Doctrine\ORM\Mapping::class => 'ORM',
        ];

        $nullableProperties = array_filter($traitProperties, fn($property) => !$property['nullable']);
        if (!empty($nullableProperties)) {
            $uses[\Webmozart\Assert\Assert::class] = null;
        }

        $dateTimeInterfaceProperties = array_filter($traitProperties, fn($property) => $property['type'] === '\DateTimeInterface');
        if (!empty($dateTimeInterfaceProperties)) {
            $uses[\Doctrine\DBAL\Types\Types::class] = null;
        }

        $properties = array_map(
            fn(array $propertyDatas) => self::defineProperty($propertyDatas),
            $traitProperties
        );

        return (new self($fileDefinition))
            ->createFileAsTrait()
            ->withUses($uses)
            ->withProperties($properties)
            ->addMembers(self::gettersForEntity($traitProperties))
            ->addMembers(self::settersForEntity($traitProperties));
    }

    private static function settersForEntity(array $traitProperties): array
    {
        foreach ($traitProperties as $property) {
            $method = new Method(Str::setter($property['fieldName']));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property['fieldName'])
                ->setType($property['type']);

            $method->getParameter($property['fieldName'])
                ->setNullable();

            $method->addBody('$this->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
                ->addBody('return $this;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function gettersForEntity(array $traitProperties): array
    {
        foreach ($traitProperties as $property) {
            if (!$property['nullable']) {
                $fieldName = Str::property($property['fieldName']);
                $method = new Method($fieldName);
                $method->setPublic()
                    ->setReturnType($property['type'])
                    ->setReturnNullable($property['nullable']);

                if (!$property['nullable']) {
                    $method->addBody('Assert::notNull($this->' . $property['fieldName'] . ');');
                }

                $method->addBody('return $this->' . $property['fieldName'] . ';');

                $methods[] = $method;
            }

            $method = new Method(Str::getter($property['fieldName']));
            $method->setPublic()
                ->setReturnType($property['type'])
                ->setReturnNullable()
                ->setBody('return $this->' . $property['fieldName'] . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private static function defineProperty(array $propertyDatas): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes()),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes())), $type)
        );

        $propertyType = self::correspondingTypes()[$type];

        $fieldName = Str::property($fieldNameRaw);

        $property = new Property($fieldName);
        $property->setPrivate()
            ->setType($propertyType)
            ->setNullable()
            ->setValue(null)
        ;

        return self::propertyUsedByEntity($property);
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

    private static function propertyUsedByEntity(Property $property): Property
    {
        $clone = clone $property;

        $columnArgs = [
            'nullable' => true, // By default, all properties are nullable, not to break schema
        ];

        $propertyType = self::doctrineCorrespondingTypes()[$clone->getType()];
        if (null !== $propertyType) {
            $columnArgs['type'] = $propertyType;
        }

        $clone->addAttribute(\Doctrine\ORM\Mapping\Column::class, $columnArgs);

        return $clone;
    }

    private static function doctrineCorrespondingTypes(): array
    {
        return [
            'string' => null,
            'int' => null,
            'float' => null,
            'bool' => null,
            '\DateTimeInterface' => class_exists(\Doctrine\DBAL\Types\Types::class) ? \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE : null,
        ];
    }
}
