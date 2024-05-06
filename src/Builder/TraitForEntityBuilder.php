<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
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

    public function createInstance(MakerConfigurationInterface|TraitForEntityMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $traitProperties = $makerConfiguration->properties();

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
            fn(array $propertyDatas) => self::defineProperty($propertyDatas, $makerConfiguration),
            $traitProperties
        );

        return parent::createInstance($makerConfiguration)
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
     * @param MakerConfigurationInterface|TraitForEntityMakerConfiguration $makerConfiguration
     * @return Property
     */
    private function defineProperty(array $propertyDatas, MakerConfigurationInterface|TraitForEntityMakerConfiguration $makerConfiguration): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes($makerConfiguration)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes($makerConfiguration))), $type)
        );

        $propertyType = self::correspondingTypes($makerConfiguration)[$type];

        $fieldName = Str::property($fieldNameRaw);

        $property = new Property($fieldName);
        $property->setPrivate()
            ->setType($propertyType)
            ->setNullable()
            ->setValue(null)
        ;

        return self::propertyUsedByEntity($property);
    }

    private function propertyUsedByEntity(Property $property): Property
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

    /**
     * @return array<string, string|Literal|null>
     */
    private function doctrineCorrespondingTypes(): array
    {
        return [
            'string' => null,
            'int' => null,
            'float' => null,
            'bool' => null,
            '\DateTimeInterface' => class_exists(\Doctrine\DBAL\Types\Types::class)
                ? new Literal('Types::DATETIME_MUTABLE')
                : null,
        ];
    }
}
