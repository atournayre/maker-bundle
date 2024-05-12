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

        $voPropertiesMap = Map::from($makerConfiguration->properties())
            ->usort(static fn(PropertyDefinition $a, PropertyDefinition $b): int => $a->fieldName <=> $b->fieldName)
        ;

        $voProperties = $voPropertiesMap->toArray();

        $properties = $voPropertiesMap
            ->map(fn(PropertyDefinition $propertyDefinition): Property => $this->defineProperty($propertyDefinition, $makerConfiguration));

        $getters = $voPropertiesMap
            ->map(fn(PropertyDefinition $propertyDefinition): Method => $this->defineGetter($propertyDefinition, $makerConfiguration));

        $nullableTrait = $this->nullableTrait($makerConfiguration);

        $uses = [
            Assert::class,
            $entityNamespace->toString(),
        ];
        $methods = [
            ...[$this->namedConstructor($voProperties, $entityNamespace)],
            ...$getters->toArray()
        ];
        $implements = [NullableInterface::class];
        $traits = [
            $nullableTrait,
            IsTrait::class,
        ];

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses($uses)
            ->setComments($this->comment())
            ->setProperties($properties->toArray())
            ->setMethods($methods)
            ->setImplements($implements)
            ->setTraits($traits)
        ;
    }

    private function entityNamespace(VoForEntityMakerConfiguration $voForEntityMakerConfiguration): UnicodeString
    {
        return UStr::create($voForEntityMakerConfiguration->relatedEntity());
    }

    private function defineGetter(PropertyDefinition $propertyDefinition, VoForEntityMakerConfiguration $voForEntityMakerConfiguration): Method
    {
        $correspondingTypes = $voForEntityMakerConfiguration->correspondingTypes();
        $propertyType = $correspondingTypes[$propertyDefinition->type];

        $body = 'return $this->__FIELD_NAME__;';
        $body = Str::replace($body, '__FIELD_NAME__', $propertyDefinition->fieldName);

        return (new Method(Str::getter($propertyDefinition->fieldName)))
            ->setPublic()
            ->setReturnType($propertyType->getType())
            ->setBody($body);
    }

    private function defineProperty(PropertyDefinition $propertyDefinition, VoForEntityMakerConfiguration $voForEntityMakerConfiguration): Property
    {
        $type = $propertyDefinition->type;
        $fieldNameRaw = $propertyDefinition->fieldName;
        $correspondingTypes = $voForEntityMakerConfiguration->correspondingTypes();
        $correspondingTypes->assertTypeExists($type, $fieldNameRaw);

        $propertyType = $correspondingTypes[$type];

        $propertyDefinition = new Property(Str::property($fieldNameRaw));
        $propertyDefinition->setPrivate()->setType($propertyType->getType());

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
