<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
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

    public function createInstance(MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $traitProperties = $makerConfiguration->properties();

        $properties = array_map(
            fn(array $propertyDatas) => self::defineProperty($propertyDatas, $makerConfiguration),
            $traitProperties
        );

        return parent::createInstance($makerConfiguration)
            ->setTrait()
            ->setProperties($properties)
            ->setMethods([
                self::gettersForObject($traitProperties, $makerConfiguration),
                self::withersForObject($traitProperties, $makerConfiguration),
            ])
        ;
    }

    /**
     * @param array{type: string, fieldName: string, nullable: bool}[] $traitProperties
     * @param MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration
     * @return Method[]
     */
    private function gettersForObject(array $traitProperties, MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::getter($property['fieldName']);
            $propertyType = $this->correspondingTypes($makerConfiguration)[$property['type']];

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($property['nullable'])
                ->setBody('return $this->' . $property['fieldName'] . ';');

            $methods[] = $method;
        }

        return $methods ?? [];
    }

    /**
     * @param array{type: string, fieldName: string, nullable: bool}[] $traitProperties
     * @param MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration
     * @return Method[]
     */
    private function withersForObject(array $traitProperties, MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration): array
    {
        foreach ($traitProperties as $property) {
            $fieldName = Str::property($property['fieldName']);
            $propertyType = $this->correspondingTypes($makerConfiguration)[$property['type']];

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

    /**
     * @param array{type: string, fieldName: string, nullable: bool} $propertyDatas
     * @param MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration
     * @return Property
     */
    private function defineProperty(array $propertyDatas, MakerConfigurationInterface|TraitForObjectMakerConfiguration $makerConfiguration): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];
        $nullable = $propertyDatas['nullable'];

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
