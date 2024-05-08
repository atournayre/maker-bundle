<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration;
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
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $traitProperties = Map::from($makerConfiguration->properties())
            ->map(function (array $propertyDatas) use ($makerConfiguration): array {
                $type = $propertyDatas['type'];

                if (!str_contains($type, '/')) {
                    return $propertyDatas;
                }

                $namespaceFromPath = Str::namespaceFromPath($type, $makerConfiguration->rootDir());
                $rootNamespace = $makerConfiguration->rootNamespace();
                $propertyDatas['type'] = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);

                return $propertyDatas;
            })
            ->toArray();

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
     * @param array{fieldName: string, type: string, nullable: bool}[] $traitProperties
     * @return Method[]
     */
    private function settersForEntity(array $traitProperties): array
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

    /**
     * @param array{fieldName: string, type: string, nullable: bool}[] $traitProperties
     * @return Method[]
     */
    private function gettersForEntity(array $traitProperties): array
    {
        foreach ($traitProperties as $property) {
            if (!$property['nullable']) {
                $fieldName = Str::getter($property['fieldName']);
                $method = new Method($fieldName);
                $method->setPublic()
                    ->setReturnType($property['type'])
                    ->setReturnNullable($property['nullable']);

                $method->addBody('Assert::notNull($this->' . $property['fieldName'] . ');');
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

    /**
     * @param array{fieldName: string, type: string, nullable: bool} $propertyDatas
     * @param TraitForEntityMakerConfiguration $makerConfiguration
     * @return Property
     */
    private function defineProperty(array $propertyDatas, TraitForEntityMakerConfiguration $makerConfiguration): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];
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
        if (null !== $propertyType) {
            $columnArgs['type'] = $propertyType;
        }

        $clone->addAttribute(\Doctrine\ORM\Mapping\Column::class, $columnArgs);

        return $clone;
    }

    private function matchDoctrineType(string $type): Literal|null
    {
        return match ($type) {
            '\DateTimeInterface' => class_exists(\Doctrine\DBAL\Types\Types::class)
                ? new Literal('Types::DATETIME_MUTABLE')
                : null,
            default => null,
        };
    }
}
