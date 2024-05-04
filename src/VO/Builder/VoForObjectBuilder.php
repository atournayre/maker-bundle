<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;

class VoForObjectBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        $voProperties = $fileDefinition->configuration()->voProperties();

        $getters = array_map(fn(array $property) => self::defineGetter($property, $fileDefinition), $voProperties);
        $withers = array_map(fn(array $property) => self::defineWither($property, $fileDefinition), $voProperties);

        $nullableTrait = MakeHelper::nullableTrait($fileDefinition);

        return static::create($fileDefinition)
            ->createFile()
            ->withUse(\Webmozart\Assert\Assert::class)
            ->addComment(self::comment())
            ->addMember(self::constructor($voProperties, $fileDefinition))
            ->addMember(self::namedConstructor($voProperties, $fileDefinition))
            ->addMembers($getters)
            ->addMembers($withers)
            ->addImplement(NullableInterface::class)
            ->addTrait($nullableTrait)
            ->addTrait(IsTrait::class)
        ;
    }

    /**
     * @param array{type: string, fieldName: string}[] $voProperties
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function constructor(array $voProperties, FileDefinition $fileDefinition): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($voProperties as $property) {
            $method->addPromotedParameter($property['fieldName'])
                ->setType(self::correspondingTypes($fileDefinition)[$property['type']])
            ;
        }

        return $method;
    }

    /**
     * @param array{type: string, fieldName: string}[] $voProperties
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function namedConstructor(array $voProperties, FileDefinition $fileDefinition): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        foreach ($voProperties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes($fileDefinition)[$property['type']])
            ;
        }

        $selfContent = implode(', $', array_column($voProperties, 'fieldName'));

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent ? '$'.$selfContent : '') . ');');

        return $method;
    }

    /**
     * @param array{type: string, fieldName: string} $property
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function defineGetter(array $property, FileDefinition $fileDefinition): Method
    {
        $propertyType = self::correspondingTypes($fileDefinition)[$property['type']];

        return (new Method(Str::getter($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    /**
     * @param array{type: string, fieldName: string} $property
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function defineWither(array $property, FileDefinition $fileDefinition): Method
    {
        $propertyType = self::correspondingTypes($fileDefinition)[$property['type']];

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
