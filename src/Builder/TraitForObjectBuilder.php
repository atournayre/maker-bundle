<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

final class TraitForObjectBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === TraitForObjectMakerConfiguration::class;
    }

    /**
     * @param TraitForObjectMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $traitProperties = $makerConfiguration->properties();

        $properties = Map::from($traitProperties)
            ->map(fn(PropertyDefinition $propertyDefinition): Property => $this->defineProperty($propertyDefinition, $makerConfiguration));

        $methods = [
            ...$this->gettersForObject($traitProperties, $makerConfiguration),
            ...$this->withersForObject($traitProperties, $makerConfiguration),
        ];

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setTrait()
            ->setProperties($properties->toArray())
            ->setMethods($methods)
        ;
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function gettersForObject(array $traitProperties, TraitForObjectMakerConfiguration $traitForObjectMakerConfiguration): array
    {
        foreach ($traitProperties as $traitProperty) {
            $fieldName = Str::getter($traitProperty->fieldName);
            $propertyType = $traitForObjectMakerConfiguration->correspondingType($traitProperty->type);

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($traitProperty->nullable)
                ->setBody('return $this->' . $traitProperty->fieldName . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function withersForObject(array $traitProperties, TraitForObjectMakerConfiguration $traitForObjectMakerConfiguration): array
    {
        foreach ($traitProperties as $traitProperty) {
            $fieldName = Str::property($traitProperty->fieldName);
            $propertyType = $traitForObjectMakerConfiguration->correspondingType($traitProperty->type);

            $method = new Method(Str::wither($fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($traitProperty->fieldName)
                ->setType($propertyType);

            $method->addBody('$clone = clone $this;')
                ->addBody('$clone->' . $traitProperty->fieldName . ' = $' . $traitProperty->fieldName . ';')
                ->addBody('return $clone;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private function defineProperty(PropertyDefinition $propertyDefinition, TraitForObjectMakerConfiguration $traitForObjectMakerConfiguration): Property
    {
        $type = $propertyDefinition->type;
        $fieldNameRaw = $propertyDefinition->fieldName;
        $nullable = $propertyDefinition->nullable;
        $correspondingTypes = $traitForObjectMakerConfiguration->correspondingTypes();
        $correspondingTypes->assertTypeExists($type, $propertyDefinition->fieldName);
        $propertyType = $traitForObjectMakerConfiguration->correspondingType($type);

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType)->setNullable($nullable);

        if ($nullable) {
            $property->setValue(null);
        }

        return $property;
    }
}
