<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class DtoBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $dtoProperties = $fileDefinition->configuration()->dtoProperties();

        $properties = array_map(
            fn (array $propertyDatas) => self::property($propertyDatas),
            $dtoProperties
        );

        return (new self($fileDefinition))
            ->createFile()
            ->withProperties($properties)
            ->addMember(self::namedConstructorFromArray($dtoProperties))
            ->addMember(self::methodValidate($dtoProperties, $fileDefinition))
        ;
    }

    private static function property(array $propertyDatas): Property
    {
        Assert::inArray(
            $propertyDatas['type'],
            array_keys(self::correspondingTypes()),
            Str::sprintf('Property "%s" should be of type %s; %s given', $propertyDatas['fieldName'], Str::implode(', ', array_keys(self::correspondingTypes())), $propertyDatas['type'])
        );

        $property = new Property($propertyDatas['fieldName']);
        $property->setVisibility('public')->setType(self::correspondingTypes()[$propertyDatas['type']]);

        $defaultValue = match ($propertyDatas['type']) {
            'string' => '',
            'integer' => 0,
            'float' => 0.0,
            'bool' => false,
            'datetime' => null,
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
