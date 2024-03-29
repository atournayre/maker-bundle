<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
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

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());

        $class->setFinal();

        foreach ($config->dtoProperties() as $property) {
            self::defineProperty($class, $property);
        }

        self::namedConstructorFromArray($class, $config->dtoProperties());

        self::methodValidate($class, $config->dtoProperties());

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

    private static function defineProperty(ClassType $class, array $property): void
    {
        Assert::inArray(
            $property['type'],
            array_keys(self::correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $property['fieldName'], implode(', ', array_keys(self::correspondingTypes())), $property['type'])
        );

        $class->addProperty($property['fieldName'])
            ->setVisibility('public')
            ->setType(self::correspondingTypes()[$property['type']]);

        $defaultValue = match ($property['type']) {
            'string' => '',
            'integer' => 0,
            'float' => 0.0,
            'bool' => false,
            'datetime' => null,
        };

        $class->getProperty($property['fieldName'])
            ->setValue($defaultValue);

        if (null === $defaultValue) {
            $class->getProperty($property['fieldName'])
                ->setNullable();
        }

        if ($property['nullable']) {
            $class->getProperty($property['fieldName'])
                ->setValue(null)
                ->setNullable();
        }
    }

    private static function methodValidate(ClassType $class, array $properties): void
    {
        $class->addMethod('validate')
            ->setPublic()
            ->setReturnType('array');

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

        $class->getMethod('validate')
            ->setBody(sprintf($errors, implode("\n", $validationErrors)));
    }

    private static function namedConstructorFromArray(ClassType $class, array $properties): void
    {
        $method = $class->addMethod('fromArray')
            ->setStatic()
            ->setPublic()
            ->setReturnType('self');

        $method->addParameter('data')
            ->setType('array');

        $method->addBody('$dto = new self();');

        foreach ($properties as $property) {
            $method->addBody(sprintf('$dto->%s = $data[\'%s\'];', $property['fieldName'], $property['fieldName']));
        }

        $method->addBody('');
        $method->addBody('return $dto;');
    }
}
