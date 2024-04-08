<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class DtoBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal();

        return (new self($fileDefinition))
            ->withFile($file)
            ->withProperties()
            ->withNamedConstructorFromArray()
            ->withMethodValidate()
        ;
    }

    private function withProperties(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $properties = $this->fileDefinition->configuration()->dtoProperties();

        foreach ($properties as $property) {
            $class->addMember($this->property($property));
        }

        return $clone;
    }

    private function property(array $propertyDatas): Property
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

    private function withNamedConstructorFromArray(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $properties = $clone->fileDefinition->configuration()->dtoProperties();

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

        $class->addMember($method);

        return $clone;
    }

    private function withMethodValidate(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $properties = $clone->fileDefinition->configuration()->dtoProperties();

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

        $method = (new Method('validate'))
            ->setPublic()
            ->setReturnType('array')
            ->setBody(sprintf($errors, implode("\n", $validationErrors)));

        $class->addMember($method);

        return $clone;
    }
}
