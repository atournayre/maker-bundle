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
            ->map(function (PropertyDefinition $propertyDefinition) use ($makerConfiguration): PropertyDefinition {
                $type = $propertyDefinition->type;

                if ($propertyDefinition->typeIsPrimitive()) {
                    return $propertyDefinition;
                }

                $namespaceFromPath = Str::namespaceFromPath($type, $makerConfiguration->rootDir());
                $rootNamespace = $makerConfiguration->rootNamespace();
                $propertyDefinition->type = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);

                return $propertyDefinition;
            })
            ->toArray();

        $properties = array_map(fn(PropertyDefinition $propertyDefinition): Property => $this->defineProperty($propertyDefinition, $makerConfiguration), $voProperties);
        $getters = array_map(fn(PropertyDefinition $propertyDefinition): Method => $this->defineGetter($propertyDefinition, $makerConfiguration), $voProperties);
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

    private function entityNamespace(VoForEntityMakerConfiguration $voForEntityMakerConfiguration): UnicodeString
    {
        return UStr::create($voForEntityMakerConfiguration->relatedEntity());
    }

    private function defineGetter(PropertyDefinition $propertyDefinition, VoForEntityMakerConfiguration $voForEntityMakerConfiguration): Method
    {
        $correspondingTypes = $this->correspondingTypes($voForEntityMakerConfiguration);
        $propertyType = $correspondingTypes[$propertyDefinition->type];

        $body = 'return $this->__FIELD_NAME__;';
        $body = Str::replace($body, '__FIELD_NAME__', $propertyDefinition->fieldName);

        return (new Method(Str::getter($propertyDefinition->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody($body);
    }

    private function defineProperty(PropertyDefinition $propertyDefinition, VoForEntityMakerConfiguration $voForEntityMakerConfiguration): Property
    {
        $type = $propertyDefinition->type;
        $fieldNameRaw = $propertyDefinition->fieldName;
        $correspondingTypes = $this->correspondingTypes($voForEntityMakerConfiguration);
        $correspondingTypes = array_combine(array_values($correspondingTypes), array_values($correspondingTypes));

        Assert::inArray(
            $type,
            $correspondingTypes,
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', $correspondingTypes), $type)
        );

        $propertyType = $correspondingTypes[$type];

        $propertyDefinition = new Property(Str::property($fieldNameRaw));
        $propertyDefinition->setPrivate()->setType($propertyType);

        return $propertyDefinition;
    }

    /**
     * @param PropertyDefinition[] $voProperties
     */
    private function namedConstructor(array $voProperties, UnicodeString $unicodeString): Method
    {
        $method = new Method('create');
        $method->setStatic()->setPublic()->setReturnType('self');

        $entityName = $unicodeString
            ->afterLast('\\')
            ->camel()
            ->toString();

        $method->addParameter($entityName)->setType($unicodeString->toString());
        $method->addBody('// Add assertions here if needed');
        $method->addBody('$self = new self();');

        foreach ($voProperties as $voProperty) {
            $linePattern = '// $self->%s = $%s->%s();';
            $line = Str::sprintf($linePattern, $voProperty->fieldName, $entityName, Str::getter($voProperty->fieldName));
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

    private function nullableTrait(VoForEntityMakerConfiguration $voForEntityMakerConfiguration): string
    {
        if (Str::startsWith($voForEntityMakerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
