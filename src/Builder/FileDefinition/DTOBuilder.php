<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class DTOBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'DTO',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $comment = [
            '',
            'Use only for request/response data structure',
            '',
            'ONLY',
            '- public properties',
            '- primitive types : string, int, float, bool, array, null, \DateTimeInterface or DTO',
            '',
            'MUST NOT',
            '- have getter/setter',
            '- have methods except `validate`',
            '- have logic in the class',
            '',
            '@object-type DTO',
        ];

        foreach ($comment as $line) {
            $fileDefinition->file->addComment($line);
        }

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
        ;

        foreach ($config->dtoProperties() as $property) {
            $class->addMember(self::property($property));
        }

        $class
            ->addMember(self::namedConstructorFromArray($config->dtoProperties()))
            ->addMember(self::methodValidate($class, $config->dtoProperties()))
        ;

        return $fileDefinition;
    }

    private static function cleanName(string $name): string
    {
        return u($name)->trimSuffix('DTO')->toString();
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

    private static function property(array $propertyDatas): Property
    {
        Assert::inArray(
            $propertyDatas['type'],
            array_keys(self::correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $propertyDatas['fieldName'], implode(', ', array_keys(self::correspondingTypes())), $propertyDatas['type'])
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

    private static function methodValidate(ClassType $class, array $properties): Method
    {
        $validationErrors = [];
        foreach ($properties as $property) {
            $if = 'if (%s) {'.PHP_EOL.'    $errors[\'%s\'] = \'validation.%s.%s.empty\';'.PHP_EOL.'}';
            $ifTest = match ($property['type']) {
                'datetime' => "null === \$this->{$property['fieldName']}",
                default => "'' == \$this->{$property['fieldName']}",
            };
            $fieldName = u($property['fieldName'])->camel()->toString();
            $dtoName = u($class->getName())->camel()->toString();

            $validationErrors[] = sprintf($if, $ifTest, $fieldName, $dtoName, $fieldName);
        }

        $errors = '$errors = [];

%s

// Add more validation rules here

return $errors;';

        return (new Method('validate'))
            ->setPublic()
            ->setReturnType('array')
            ->setBody(sprintf($errors, implode("\n", $validationErrors)));
    }

    private static function namedConstructorFromArray(array $properties): Method
    {
        $bodyParts = [];
        $bodyParts[] = '$dto = new self();';
        foreach ($properties as $property) {
            $bodyParts[] = sprintf('$dto->%s = $data[\'%s\'];', $property['fieldName'], $property['fieldName']);
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
}
