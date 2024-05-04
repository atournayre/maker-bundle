<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Null\NullableInterface;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class DtoBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        $dtoProperties = $fileDefinition->configuration()->dtoProperties();

        $properties = array_map(
            fn (array $propertyDatas) => self::property($propertyDatas, $fileDefinition),
            $dtoProperties
        );

        $nullableTrait = MakeHelper::nullableTrait($fileDefinition);

        return static::create($fileDefinition)
            ->createFile()
            ->withProperties($properties)
            ->addMember(self::namedConstructorFromArray($dtoProperties))
            ->addMember(self::methodValidate($dtoProperties, $fileDefinition))
            ->addImplement(NullableInterface::class)
            ->addTrait($nullableTrait)
        ;
    }

    /**
     * @param array{fieldName: string, type: string, nullable: bool}[] $propertyDatas
     * @param FileDefinition $fileDefinition
     * @return Property
     */
    private static function property(array $propertyDatas, FileDefinition $fileDefinition): Property
    {
        /** @var string $type */
        $type = $propertyDatas['type'];
        Assert::inArray(
            $type,
            array_keys(self::correspondingTypes($fileDefinition)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $propertyDatas['fieldName'], Str::implode(', ', array_keys(self::correspondingTypes($fileDefinition))), $type)
        );

        $property = new Property($propertyDatas['fieldName']);
        $property->setVisibility('public')->setType(self::correspondingTypes($fileDefinition)[$type]);

        $defaultValue = match ($type) {
            'string' => '',
            'integer' => 0,
            'float' => 0.0,
            'bool' => false,
            default => null,
        };

        $property->setValue($defaultValue);

        if (null === $defaultValue) {
            $property->setNullable();
        }

        if ($propertyDatas['nullable']) {
            $property->setValue(null)->setNullable();
        }

        return $property;
    }

    /**
     * @param array{fieldName: string, type: string}[] $dtoPoperties
     * @return Method
     */
    private static function namedConstructorFromArray(array $dtoPoperties): Method
    {
        $bodyParts = [];
        $bodyParts[] = '$dto = new self();';
        foreach ($dtoPoperties as $property) {
            $bodyParts[] = Str::sprintf('$dto->%s = $data[\'%s\'];', $property['fieldName'], $property['fieldName']);
        }
        $bodyParts[] = '';
        $bodyParts[] = 'return $dto;';

        $method = new Method('fromArray');
        $method->setStatic()->setPublic()->setReturnType('self');
        $method->addParameter('data')->setType('array');

        foreach ($bodyParts as $line) {
            $method->addBody($line);
        }

        return $method;
    }

    /**
     * @param array{fieldName: string, type: string}[] $dtoPoperties
     * @param FileDefinition $fileDefinition
     * @return Method
     */
    private static function methodValidate(array $dtoPoperties, FileDefinition $fileDefinition): Method
    {
        $className = $fileDefinition->classname();

        $validationErrors = [];
        foreach ($dtoPoperties as $property) {
            $if = 'if (%s) {'.PHP_EOL.'    $errors[\'%s\'] = \'validation.%s.%s.empty\';'.PHP_EOL.'}';
            $ifTest = match ($property['type']) {
                'datetime' => "null === \$this->{$property['fieldName']}",
                default => "'' == \$this->{$property['fieldName']}",
            };
            $fieldName = Str::property($property['fieldName']);
            $dtoName = Str::asCamelCase($className);

            $validationErrors[] = Str::sprintf($if, $ifTest, $fieldName, $dtoName, $fieldName);
        }

        $errors = '$errors = [];

%s

// Add more validation rules here

return $errors;';

        $body = Str::sprintf($errors, Str::implode("\n", $validationErrors));

        return (new Method('validate'))
            ->setPublic()
            ->setReturnType('array')
            ->setBody($body);
    }
}
