<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use App\Trait\NullableTrait;
use Atournayre\Bundle\MakerBundle\Config\VoForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
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

    public function createInstance(MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $entityNamespace = self::entityNamespace($makerConfiguration);
        $voProperties = $makerConfiguration->properties();

        $properties = array_map(fn(array $property): Property => self::defineProperty($property, $makerConfiguration), $voProperties);
        $getters = array_map(fn(array $property): Method => self::defineGetter($property, $makerConfiguration), $voProperties);
        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createInstance($makerConfiguration)
            ->setUses([
                Assert::class,
                $entityNamespace->toString(),
            ])
            ->setComments($this->comment())
            ->setProperties($properties)
            ->setMethods(array_merge([$this->namedConstructor($voProperties, $entityNamespace)], $getters))
            ->setImplements([$nullableTrait])
            ->setTraits([
                $nullableTrait,
                IsTrait::class,
            ])

        ;
    }

    private function entityNamespace(MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration): UnicodeString
    {
        return UStr::create($makerConfiguration->voRelatedToAnEntityWithRootNamespace());
    }

    /**
     * @param array{fieldName: string, type: string} $property
     * @param MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration
     * @return Method
     */
    private function defineGetter(array $property, MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration): Method
    {
        $propertyType = $this->correspondingTypes($makerConfiguration)[$property['type']];

        $body = 'return $this->__FIELD_NAME__;';
        $body = Str::replace($body, '__FIELD_NAME__', $property['fieldName']);

        return (new Method(Str::getter($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody($body);
    }

    /**
     * @param array{fieldName: string, type: string} $property
     * @param MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration
     * @return Property
     */
    private function defineProperty(array $property, MakerConfigurationInterface|VoForEntityMakerConfiguration $makerConfiguration): Property
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];

        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes($makerConfiguration)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys($this->correspondingTypes($makerConfiguration))), $type)
        );

        $propertyType = $this->correspondingTypes($makerConfiguration)[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    /**
     * @param array{fieldName: string, type: string}[] $voProperties
     * @param UnicodeString $entityNamespace
     * @return Method
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
            $line = Str::sprintf($linePattern, $property['fieldName'], $entityName, Str::getter($property['fieldName']));
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

    private function nullableTrait(MakerConfigurationInterface $makerConfiguration): string
    {
        if (Str::startsWith($makerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
