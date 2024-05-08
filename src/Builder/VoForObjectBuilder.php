<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

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

        $getters = array_map(fn(PropertyDefinition $property) => $this->defineGetter($property, $makerConfiguration), $voProperties);
        $withers = array_map(fn(PropertyDefinition $property) => $this->defineWither($property, $makerConfiguration), $voProperties);

        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                \Webmozart\Assert\Assert::class,
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
    private function constructor(array $voProperties, VoForObjectMakerConfiguration $makerConfiguration): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($voProperties as $property) {
            $method->addPromotedParameter($property->fieldName)
                ->setType($this->correspondingTypes($makerConfiguration)[$property->type])
            ;
        }

        return $method;
    }

    /**
     * @param PropertyDefinition[] $voProperties
     */
    private function namedConstructor(array $voProperties, VoForObjectMakerConfiguration $makerConfiguration): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        foreach ($voProperties as $property) {
            $method->addParameter($property->fieldName)
                ->setType($this->correspondingTypes($makerConfiguration)[$property->type])
            ;
        }

        $fieldNames = array_map(fn(PropertyDefinition $property) => $property->fieldName, $voProperties);
        $selfContent = implode(', $', $fieldNames);

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent !== '' && $selfContent !== '0' ? '$'.$selfContent : '') . ');');

        return $method;
    }

    private function defineGetter(PropertyDefinition $property, VoForObjectMakerConfiguration $makerConfiguration): Method
    {
        $propertyType = $this->correspondingTypes($makerConfiguration)[$property->type];

        return (new Method(Str::getter($property->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property->fieldName . ';');
    }

    private function defineWither(PropertyDefinition $property, VoForObjectMakerConfiguration $makerConfiguration): Method
    {
        $propertyType = $this->correspondingTypes($makerConfiguration)[$property->type];

        $fieldName = Str::property($property->fieldName);

        $method = new Method(Str::wither($fieldName));
        $method
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($property->fieldName)
            ->setType($propertyType)
        ;

        $method
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $property->fieldName . ' = $' . $property->fieldName . ';')
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

    private function nullableTrait(VoForObjectMakerConfiguration $makerConfiguration): string
    {
        if (Str::startsWith($makerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}