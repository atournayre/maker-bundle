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
            ->map(function (PropertyDefinition $propertyDatas) use ($makerConfiguration): PropertyDefinition {
                $type = $propertyDatas->type;

                if ($propertyDatas->typeIsPrimitive()) {
                    return $propertyDatas;
                }

                $namespaceFromPath = Str::namespaceFromPath($type, $makerConfiguration->rootDir());
                $rootNamespace = $makerConfiguration->rootNamespace();
                $propertyDatas->type = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);

                return $propertyDatas;
            })
            ->toArray();

        $uses = [
            Mapping::class => 'ORM',
        ];

        $nullableProperties = array_filter($traitProperties, fn(PropertyDefinition $property) => !$property->nullable);
        if ($nullableProperties !== []) {
            $uses[Assert::class] = null;
        }

        $dateTimeInterfaceProperties = array_filter($traitProperties, fn(PropertyDefinition $property) => $property->typeIsDateTimeInterface());
        if ($dateTimeInterfaceProperties !== []) {
            $uses[Types::class] = null;
        }

        $properties = array_map(
            fn(array $propertyDatas) => $this->defineProperty($propertyDatas, $makerConfiguration),
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
        foreach ($traitProperties as $property) {
            $method = new Method(Str::setter($property->fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property->fieldName)
                ->setType($property->type);

            $method->getParameter($property->fieldName)
                ->setNullable();

            $method->addBody('$this->' . $property->fieldName . ' = $' . $property->fieldName . ';')
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
        foreach ($traitProperties as $property) {
            if (!$property->nullable) {
                $fieldName = Str::getter($property->fieldName);
                $method = new Method($fieldName);
                $method->setPublic()
                    ->setReturnType($property->type)
                    ->setReturnNullable($property->nullable);

                $method->addBody('Assert::notNull($this->' . $property->fieldName . ');');
                $method->addBody('return $this->' . $property->fieldName . ';');

                $methods[] = $method;
            }

            $method = new Method(Str::getter($property->fieldName));
            $method->setPublic()
                ->setReturnType($property->type)
                ->setReturnNullable()
                ->setBody('return $this->' . $property->fieldName . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private function defineProperty(PropertyDefinition $propertyDatas, TraitForEntityMakerConfiguration $makerConfiguration): Property
    {
        $type = $propertyDatas->type;
        $fieldNameRaw = $propertyDatas->fieldName;
        $correspondingTypes = $this->correspondingTypes($makerConfiguration);
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
