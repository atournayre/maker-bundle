<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Aimeos\Map;
use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use App\Trait\NullableTrait;
use Atournayre\Bundle\MakerBundle\Config\VoForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Helper\UStr;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

final class VoForEntityBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === VoForEntityMakerConfiguration::class;
    }

    /**
     * @param VoForEntityMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $entityNamespace = self::entityNamespace($makerConfiguration);
        $voProperties = Map::from($makerConfiguration->properties())
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

        $properties = array_map(fn(array $property): Property => $this->defineProperty($property, $makerConfiguration), $voProperties);
        $getters = array_map(fn(array $property): Method => $this->defineGetter($property, $makerConfiguration), $voProperties);
        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                Assert::class,
                $entityNamespace->toString(),
            ])
            ->setComments($this->comment())
            ->setProperties($properties)
            ->setMethods(array_merge([$this->namedConstructor($voProperties, $entityNamespace)], $getters))
            ->setImplements([NullableInterface::class])
            ->setTraits([
                $nullableTrait,
                IsTrait::class,
            ])
        ;
    }

    private function entityNamespace(VoForEntityMakerConfiguration $makerConfiguration): UnicodeString
    {
        return UStr::create($makerConfiguration->relatedEntity());
    }

    private function defineGetter(PropertyDefinition $property, VoForEntityMakerConfiguration $makerConfiguration): Method
    {
        $correspondingTypes = $this->correspondingTypes($makerConfiguration);
        $propertyType = $correspondingTypes[$property->type];

        $body = 'return $this->__FIELD_NAME__;';
        $body = Str::replace($body, '__FIELD_NAME__', $property->fieldName);

        return (new Method(Str::getter($property->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody($body);
    }

    private function defineProperty(PropertyDefinition $property, VoForEntityMakerConfiguration $makerConfiguration): Property
    {
        $type = $property->type;
        $fieldNameRaw = $property->fieldName;
        $correspondingTypes = $this->correspondingTypes($makerConfiguration);
        $correspondingTypes = array_combine(array_values($correspondingTypes), array_values($correspondingTypes));

        Assert::inArray(
            $type,
            $correspondingTypes,
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', $correspondingTypes), $type)
        );

        $propertyType = $correspondingTypes[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    /**
     * @param PropertyDefinition[] $voProperties
     */
    private function namedConstructor(array $voProperties, UnicodeString $entityNamespace): Method
    {
        $method = new Method('create');
        $method->setStatic()->setPublic()->setReturnType('self');

        $entityName = $entityNamespace
            ->afterLast('\\')
            ->camel()
            ->toString();

        $method->addParameter($entityName)->setType($entityNamespace->toString());
        $method->addBody('// Add assertions here if needed');
        $method->addBody('$self = new self();');

        foreach ($voProperties as $property) {
            $linePattern = '// $self->%s = $%s->%s();';
            $line = Str::sprintf($linePattern, $property->fieldName, $entityName, Str::getter($property->fieldName));
            $method->addBody($line);
        }

        $method->addBody('return $self;');

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

    private function nullableTrait(VoForEntityMakerConfiguration $makerConfiguration): string
    {
        if (Str::startsWith($makerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
