<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Webmozart\Assert\Assert;
use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use App\Trait\NullableTrait;
use Atournayre\Bundle\MakerBundle\Config\VoForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class VoForObjectBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === VoForObjectMakerConfiguration::class;
    }

    /**
     * @param VoForObjectMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $voProperties = $makerConfiguration->properties();

        $getters = array_map(fn(PropertyDefinition $propertyDefinition) => $this->defineGetter($propertyDefinition, $makerConfiguration), $voProperties);
        $withers = array_map(fn(PropertyDefinition $propertyDefinition) => $this->defineWither($propertyDefinition, $makerConfiguration), $voProperties);

        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                Assert::class,
            ])
            ->setComments($this->comment())
            ->setMethods([
                $this->constructor($voProperties, $makerConfiguration),
                $this->namedConstructor($voProperties, $makerConfiguration),
                ...$getters,
                ...$withers,
            ])
            ->setImplements([
                NullableInterface::class,
            ])
            ->setTraits([
                $nullableTrait,
                IsTrait::class,
            ])
        ;
    }

    /**
     * @param PropertyDefinition[] $voProperties
     */
    private function constructor(array $voProperties, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($voProperties as $voProperty) {
            $method->addPromotedParameter($voProperty->fieldName)
                ->setType($this->correspondingTypes($voForObjectMakerConfiguration)[$voProperty->type])
            ;
        }

        return $method;
    }

    /**
     * @param PropertyDefinition[] $voProperties
     */
    private function namedConstructor(array $voProperties, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        foreach ($voProperties as $property) {
            $method->addParameter($property->fieldName)
                ->setType($this->correspondingTypes($voForObjectMakerConfiguration)[$property->type])
            ;
        }

        $fieldNames = array_map(fn(PropertyDefinition $property) => $property->fieldName, $voProperties);
        $selfContent = implode(', $', $fieldNames);

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent !== '' && $selfContent !== '0' ? '$'.$selfContent : '') . ');');

        return $method;
    }

    private function defineGetter(PropertyDefinition $propertyDefinition, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $propertyType = $this->correspondingTypes($voForObjectMakerConfiguration)[$propertyDefinition->type];

        return (new Method(Str::getter($propertyDefinition->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $propertyDefinition->fieldName . ';');
    }

    private function defineWither(PropertyDefinition $propertyDefinition, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $propertyType = $this->correspondingTypes($voForObjectMakerConfiguration)[$propertyDefinition->type];

        $fieldName = Str::property($propertyDefinition->fieldName);

        $method = new Method(Str::wither($fieldName));
        $method
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($propertyDefinition->fieldName)
            ->setType($propertyType)
        ;

        $method
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $propertyDefinition->fieldName . ' = $' . $propertyDefinition->fieldName . ';')
            ->addBody('return $clone;');
        return $method;
    }

    /**
     * @return string[]
     */
    private function comment(): array
    {
        return [
            '',
            'ONLY',
            '- primitive types : string, int, float, bool, array, \DateTimeInterface or VO',
            '',
            'MUST',
            '- check validity of the data on creation',
            '- be immutable',
            '- be final',
            '',
            'SHOULD',
            '- have a named constructor',
            '- have withers',
            '- have logic',
            '',
            'MUST NOT',
            '- have setters',
            '',
            '@object-type VO',
        ];
    }

    private function nullableTrait(VoForObjectMakerConfiguration $voForObjectMakerConfiguration): string
    {
        if (Str::startsWith($voForObjectMakerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
