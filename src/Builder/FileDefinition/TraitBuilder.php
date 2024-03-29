<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitType;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class TraitBuilder implements FileDefinitionBuilderInterface
{
    public static function buildAccessorsOnly(
        MakerConfig $config,
        string      $namespace,
        string      $name,
    ): FileDefinitionBuilder
    {
        $name = self::defineName($name.'Accessors', $config);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Trait', $config);

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        foreach ($config->traitProperties() as $property) {
            if ($config->traitIsUsedByEntity()) {
                self::definePropertyAccessorsForEntity($trait, $property);
            } else {
                self::definePropertyAccessorsForObject($trait, $property);
            }
        }

        return $fileDefinition;
    }

    private static function defineName(string $name, MakerConfig $config): string
    {
        if ($config->traitIsUsedByEntity()) {
            $name .= 'Entity';
        }

        return $name;
    }

    public static function build(
        MakerConfig $config,
        string      $namespace = 'Traits',
        string      $name = ''
    ): FileDefinitionBuilder
    {
        $name = self::defineName($name, $config);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Trait', $config);

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        foreach ($config->traitProperties() as $property) {
            self::defineProperty($trait, $property, $config);

            if ($config->traitIsUsedByEntity()) {
                self::definePropertyAccessorsForEntity($trait, $property);
            } else {
                self::definePropertyAccessorsForObject($trait, $property);
            }
        }

        return $fileDefinition;
    }

    private static function defineProperty(TraitType $trait, array $property, MakerConfig $config): void
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];
        $nullable = $property['nullable'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, implode(', ', array_keys(self::correspondingTypes())), $type)
        );

        $propertyType = self::correspondingTypes()[$type];

        $fieldName = u($fieldNameRaw)->camel()->toString();

        $trait->addProperty($fieldName)
            ->setPrivate()
            ->setType($propertyType)
            ->setNullable($nullable);

        if ($nullable) {
            $trait->getProperty($fieldName)
                ->setValue(null);
        }

        if ($config->traitIsUsedByEntity()) {
            self::propertyUsedByEntity($trait, $trait->getProperty($fieldName));
        }
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

    private static function definePropertyAccessorsForEntity(TraitType $trait, array $property): void
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];
        $nullable = $property['nullable'];
        $propertyType = self::correspondingTypes()[$type];
        $fieldName = u($fieldNameRaw)->camel()->toString();

        $trait->addMethod($fieldName)
            ->setPublic()
            ->setReturnType($propertyType)
            ->setReturnNullable($nullable)
            ->setBody('return $this->' . $fieldNameRaw . ';');

        $set = u($fieldName)->title()->prepend('set')->camel()->toString();

        $trait->addMethod($set)
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($fieldNameRaw)
            ->setType($propertyType);

        $trait->getMethod($set)
            ->getParameter($fieldNameRaw)
            ->setNullable($nullable);

        $trait->getMethod($set)
            ->addBody('$this->' . $fieldNameRaw . ' = $' . $fieldNameRaw . ';')
            ->addBody('return $this;');
    }

    private static function propertyUsedByEntity(TraitType $trait, Property $property): void
    {
        $columnArgs = [
            'nullable' => true, // By default, all properties are nullable, not to break schema
        ];

        $propertyType = self::doctrineCorrespondingTypes()[$property->getType()];
        if (null !== $propertyType) {
            $columnArgs['type'] = $propertyType;
        }

        $property
            ->addAttribute(\Doctrine\ORM\Mapping\Column::class, $columnArgs);

        $namespace = $trait->getNamespace();
        $namespace->addUse(\Doctrine\ORM\Mapping::class, 'ORM');

        if ($property->getType() === '\DateTimeInterface' && null !== $propertyType) {
            $namespace->addUse(\Doctrine\DBAL\Types\Types::class);
        }
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

    private static function definePropertyAccessorsForObject(TraitType $trait, array $property): void
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];
        $nullable = $property['nullable'];
        $propertyType = self::correspondingTypes()[$type];
        $fieldName = u($fieldNameRaw)->camel()->toString();

        $trait->addMethod($fieldName)
            ->setPublic()
            ->setReturnType($propertyType)
            ->setReturnNullable($nullable)
            ->setBody('return $this->' . $fieldNameRaw . ';');

        $with = u($fieldName)->title()->prepend('with')->camel()->toString();

        $trait->addMethod($with)
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($fieldNameRaw)
            ->setType($propertyType);

        $trait->getMethod($with)
            ->getParameter($fieldNameRaw)
            ->setNullable($nullable);

        $trait->getMethod($with)
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $fieldNameRaw . ' = $' . $fieldNameRaw . ';')
            ->addBody('return $clone;');
    }

    public static function buildPropertyOnly(
        MakerConfig $config,
        string      $namespace,
        string      $name,
    ): FileDefinitionBuilder
    {
        $name = self::defineName($name . 'Property', $config);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Trait', $config);

        $trait = $fileDefinition->file->addTrait($fileDefinition->fullName());

        foreach ($config->traitProperties() as $property) {
            self::defineProperty($trait, $property, $config);
        }

        return $fileDefinition;
    }
}
