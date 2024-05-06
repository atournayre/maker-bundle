<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Helper\UStr;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

class VoForEntityBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        $entityNamespace = self::entityNamespace($fileDefinition);
        $voProperties = $fileDefinition->configuration()->voProperties();

        $properties = array_map(fn(array $property): Property => self::defineProperty($property, $fileDefinition), $voProperties);
        $getters = array_map(fn(array $property): Method => self::defineGetter($property, $fileDefinition), $voProperties);
        $nullableTrait = MakeHelper::nullableTrait($fileDefinition);

        return static::create($fileDefinition)
            ->createFile()
            ->withUse(\Webmozart\Assert\Assert::class)
            ->withUse($entityNamespace->toString())
            ->addComment(self::comment())
            ->addMember(self::namedConstructor($voProperties, $entityNamespace))
            ->withProperties($properties)
            ->addMembers($getters)
            ->addImplement(NullableInterface::class)
            ->addTrait($nullableTrait)
            ->addTrait(IsTrait::class)
            ;
    }

    private static function entityNamespace(FileDefinition $fileDefinition): UnicodeString
    {
        return UStr::create($fileDefinition->configuration()->voRelatedToAnEntityWithRootNamespace());
    }

    /**
     * @param array{fieldName: string, type: string} $property
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function defineGetter(array $property, FileDefinition $fileDefinition): Method
    {
        $propertyType = self::correspondingTypes($fileDefinition)[$property['type']];

        $body = 'return $this->__FIELD_NAME__;';
        $body = Str::replace($body, '__FIELD_NAME__', $property['fieldName']);

        return (new Method(Str::getter($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody($body);
    }

    /**
     * @param array{fieldName: string, type: string} $property
     * @param FileDefinition $fileDefinition
     * @return Property
     */
    private static function defineProperty(array $property, FileDefinition $fileDefinition): Property
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes($fileDefinition)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes($fileDefinition))), $type)
        );

        $propertyType = self::correspondingTypes($fileDefinition)[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    /**
     * @param array{fieldName: string, type: string}[] $voProperties
     * @param UnicodeString $entityNamespace
     * @return Method
     */
    private static function namedConstructor(array $voProperties, UnicodeString $entityNamespace): Method
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
    private static function comment(): array
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
}
