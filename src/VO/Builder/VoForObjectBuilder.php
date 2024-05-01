<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class VoForObjectBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $voProperties = $fileDefinition->configuration()->voProperties();
        $getters = array_map(fn($property) => self::defineGetter($property), $voProperties);
        $withers = array_map(fn($property) => self::defineWither($property), $voProperties);
        $nullableTrait = MakeHelper::nullableTrait($fileDefinition);

        return (new self($fileDefinition))
            ->createFile()
            ->withUse(\Webmozart\Assert\Assert::class)
            ->addComment(self::comment())
            ->addMember(self::constructor($voProperties))
            ->addMember(self::namedConstructor($voProperties))
            ->addMembers($getters)
            ->addMembers($withers)
            ->addImplement(NullableInterface::class)
            ->addTrait($nullableTrait)
            ->addTrait(IsTrait::class)
        ;
    }

    private static function constructor(array $voProperties): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($voProperties as $property) {
            $method->addPromotedParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']])
            ;
        }

        return $method;
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

    private static function namedConstructor(array $voProperties): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        foreach ($voProperties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']])
            ;
        }

        $selfContent = implode(', $', array_column($voProperties, 'fieldName'));

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent ? '$'.$selfContent : '') . ');');

        return $method;
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

        $fieldName = Str::property($fieldNameRaw);

        $property = new Property($fieldName);
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    private static function defineGetter(array $property): Method
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        return (new Method(Str::getter($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    private static function defineWither(array $property): Method
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = Str::property($property['fieldName']);

        $method = new Method(Str::wither($fieldName));
        $method
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($property['fieldName'])
            ->setType($propertyType)
        ;

        $method
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
            ->addBody('return $clone;');
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
