<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

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

        $properties = array_map(
            fn(PropertyDefinition $propertyDatas) => $this->defineProperty($propertyDatas, $makerConfiguration),
            $traitProperties
        );

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setTrait()
            ->setProperties($properties)
            ->setMethods([
                ...$this->gettersForObject($traitProperties, $makerConfiguration),
                ...$this->withersForObject($traitProperties, $makerConfiguration),
            ])
        ;
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function gettersForObject(array $traitProperties, TraitForObjectMakerConfiguration $makerConfiguration): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::getter($property->fieldName);
            $propertyType = $this->correspondingTypes($makerConfiguration)[$property->type];

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($property->nullable)
                ->setBody('return $this->' . $property->fieldName . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    /**
     * @param PropertyDefinition[] $traitProperties
     * @return Method[]
     */
    private function withersForObject(array $traitProperties, TraitForObjectMakerConfiguration $makerConfiguration): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::property($property->fieldName);
            $propertyType = $this->correspondingTypes($makerConfiguration)[$property->type];

            $method = new Method(Str::wither($fieldName));
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property->fieldName)
                ->setType($propertyType);

            $method->addBody('$clone = clone $this;')
                ->addBody('$clone->' . $property->fieldName . ' = $' . $property->fieldName . ';')
                ->addBody('return $clone;');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    private function defineProperty(PropertyDefinition $propertyDatas, TraitForObjectMakerConfiguration $makerConfiguration): Property
    {
        $type = $propertyDatas->type;
        $fieldNameRaw = $propertyDatas->fieldName;
        $nullable = $propertyDatas->nullable;

        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes($makerConfiguration)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys($this->correspondingTypes($makerConfiguration))), $type)
        );

        $propertyType = $this->correspondingTypes($makerConfiguration)[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType)->setNullable($nullable);

        if ($nullable) {
            $property->setValue(null);
        }

        return $property;
    }
}
