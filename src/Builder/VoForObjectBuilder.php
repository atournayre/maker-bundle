<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Aimeos\Map;
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

        $constructor = $this->constructor($voProperties, $makerConfiguration);
        $namedConstructor = $this->namedConstructor($voProperties, $makerConfiguration);

        $methods = Map::from([
            $constructor->getName() => $constructor,
            $namedConstructor->getName() => $namedConstructor,
        ]);
        foreach ($voProperties as $voProperty) {
            $getter = $this->defineGetter($voProperty, $makerConfiguration);
            $methods->set($getter->getName(), $getter);
            $wither = $this->defineWither($voProperty, $makerConfiguration);
            $methods->set($wither->getName(), $wither);
        }

        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                Assert::class,
            ])
            ->setComments($this->comment())
            ->setMethods($methods->toArray())
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
                ->setPrivate()
                ->setType($voForObjectMakerConfiguration->correspondingType($voProperty->type))
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
                ->setType($voForObjectMakerConfiguration->correspondingType($property->type))
            ;
        }

        $fieldNames = array_map(static fn(PropertyDefinition $property): string => $property->fieldName, $voProperties);
        $selfContent = implode(', $', $fieldNames);

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent !== '' && $selfContent !== '0' ? '$'.$selfContent : '') . ');');

        return $method;
    }

    private function defineGetter(PropertyDefinition $propertyDefinition, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $propertyType = $voForObjectMakerConfiguration->correspondingType($propertyDefinition->type);

        return (new Method(Str::getter($propertyDefinition->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $propertyDefinition->fieldName . ';');
    }

    private function defineWither(PropertyDefinition $propertyDefinition, VoForObjectMakerConfiguration $voForObjectMakerConfiguration): Method
    {
        $propertyType = $voForObjectMakerConfiguration->correspondingType($propertyDefinition->type);

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
