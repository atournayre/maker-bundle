<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Null\NullableInterface;
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
    public static function build(FileDefinition $fileDefinition): self
    {
        $entityNamespace = self::entityNamespace($fileDefinition);
        $voProperties = $fileDefinition->configuration()->voProperties();

        $properties = array_map(fn($property) => self::defineProperty($property), $voProperties);
        $getters = array_map(fn($property) => self::defineGetter($property), $voProperties);
        $nullableTrait = MakeHelper::nullableTrait($fileDefinition);

        return (new self($fileDefinition))
            ->createFile()
            ->withUse(\Webmozart\Assert\Assert::class)
            ->withUse($entityNamespace->toString())
            ->addComment(self::comment())
            ->addMember(self::namedConstructor($voProperties, $entityNamespace))
            ->withProperties($properties)
            ->addMembers($getters)
            ->addImplement(NullableInterface::class)
            ->addTrait($nullableTrait)
            ;
    }

    private static function entityNamespace(FileDefinition $fileDefinition): UnicodeString
    {
        $config = $fileDefinition->configuration();
        return UStr::prefixByRootNamespace($config->voRelatedToAnEntity(), $config->rootNamespace());
    }

    private static function defineGetter(array $property): Method
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        return (new Method(Str::property($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    private static function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
        ];
    }

    private static function defineProperty(array $property): Property
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];

        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes()),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys(self::correspondingTypes())), $type)
        );

        $propertyType = self::correspondingTypes()[$type];

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

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
