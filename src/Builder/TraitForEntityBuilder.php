<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Doctrine\ORM\Mapping;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

final class TraitForEntityBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === TraitForEntityMakerConfiguration::class;
    }

    /**
     * @param TraitForEntityMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $traitProperties = Map::from($makerConfiguration->properties())
            ->map(function (PropertyDefinition $propertyDefinition) use ($makerConfiguration): PropertyDefinition {
                $type = $propertyDefinition->type;

                if ($propertyDefinition->typeIsPrimitive()) {
                    return $propertyDefinition;
                }

                $namespaceFromPath = Str::namespaceFromPath($type, $makerConfiguration->rootDir());
                $rootNamespace = $makerConfiguration->rootNamespace();
                $propertyDefinition->type = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);

                return $propertyDefinition;
            })
            ->toArray();

        $uses = [
            Mapping::class => 'ORM',
        ];

        $nullableProperties = array_filter($traitProperties, fn(PropertyDefinition $propertyDefinition) => !$propertyDefinition->nullable);
        if ($nullableProperties !== []) {
            $uses[Assert::class] = null;
        }

        $dateTimeInterfaceProperties = array_filter($traitProperties, fn(PropertyDefinition $propertyDefinition) => $propertyDefinition->typeIsDateTimeInterface());
        if ($dateTimeInterfaceProperties !== []) {
            $uses[Types::class] = null;
        }

        $properties = array_map(
            fn(PropertyDefinition $propertyDefinition) => $this->defineProperty($propertyDefinition, $makerConfiguration),
            $traitProperties
        );

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setTrait()
            ->setUses($uses)
            ->setProperties($properties)
            ->setMethods([
                ...$this->gettersForEntity($traitProperties),
                ...$this->settersForEntity($traitProperties),
            ])
        ;
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function settersForEntity(array $traitProperties): array
    {
        foreach ($traitProperties as $traitProperty) {
            $method = new Method(Str::setter($traitProperty->fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($traitProperty->fieldName)
                ->setType($traitProperty->type);

            $method->getParameter($traitProperty->fieldName)
                ->setNullable();

            $method->addBody('$this->' . $traitProperty->fieldName . ' = $' . $traitProperty->fieldName . ';')
                ->addBody('return $this;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function gettersForEntity(array $traitProperties): array
    {
        foreach ($traitProperties as $traitProperty) {
            if (!$traitProperty->nullable) {
                $fieldName = Str::getter($traitProperty->fieldName);
                $method = new Method($fieldName);
                $method->setPublic()
                    ->setReturnType($traitProperty->type)
                    ->setReturnNullable($traitProperty->nullable);

                $method->addBody('Assert::notNull($this->' . $traitProperty->fieldName . ');');
                $method->addBody('return $this->' . $traitProperty->fieldName . ';');

                $methods[] = $method;
            }

            $method = new Method(Str::getter($traitProperty->fieldName));
            $method->setPublic()
                ->setReturnType($traitProperty->type)
                ->setReturnNullable()
                ->setBody('return $this->' . $traitProperty->fieldName . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private function defineProperty(PropertyDefinition $propertyDefinition, TraitForEntityMakerConfiguration $traitForEntityMakerConfiguration): Property
    {
        $type = $propertyDefinition->type;
        $fieldNameRaw = $propertyDefinition->fieldName;
        $correspondingTypes = $this->correspondingTypes($traitForEntityMakerConfiguration);
        $correspondingTypes = array_combine(array_values($correspondingTypes), array_values($correspondingTypes));

        Assert::inArray(
            $type,
            $correspondingTypes,
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', $correspondingTypes), $type)
        );

        $propertyType = $correspondingTypes[$type];

        $fieldName = Str::property($fieldNameRaw);

        $property = new Property($fieldName);
        $property->setPrivate()
            ->setType($propertyType)
            ->setNullable()
            ->setValue(null)
        ;

        return $this->propertyUsedByEntity($property);
    }

    private function propertyUsedByEntity(Property $property): Property
    {
        $clone = clone $property;

        $columnArgs = [
            'nullable' => true, // By default, all properties are nullable, not to break schema
        ];

        $propertyType = $this->matchDoctrineType($clone->getType());
        if ($propertyType instanceof Literal) {
            $columnArgs['type'] = $propertyType;
        }

        $clone->addAttribute(Column::class, $columnArgs);

        return $clone;
    }

    private function matchDoctrineType(string $type): Literal|null
    {
        return match ($type) {
            '\DateTimeInterface' => class_exists(Types::class)
                ? new Literal('Types::DATETIME_MUTABLE')
                : null,
            default => null,
        };
    }
}
