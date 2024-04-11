<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class TraitForObjectBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->addTrait($fileDefinition->fullName());

        return (new self($fileDefinition))
            ->withFile($file)
            ->withProperties()
            ->withPropertyGettersForObject()
            ->withPropertyWithersForObject();
    }

    private function withPropertyGettersForObject(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $traitProperties = $clone->fileDefinition->configuration()->traitProperties();

        foreach ($traitProperties as $property) {
            $fieldName = u($property['fieldName'])->camel()->toString();
            $propertyType = $this->correspondingTypes()[$property['type']];

            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($propertyType)
                ->setReturnNullable($property['nullable'])
                ->setBody('return $this->' . $property['fieldName'] . ';');

            $class->addMember($method);
        }

        return $clone;
    }

    private function withPropertyWithersForObject(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $traitProperties = $clone->fileDefinition->configuration()->traitProperties();

        foreach ($traitProperties as $property) {
            $fieldName = u($property['fieldName'])->camel()->toString();
            $with = u($fieldName)->title()->prepend('with')->camel()->toString();
            $propertyType = $this->correspondingTypes()[$property['type']];

            $method = new Method($with);
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property['fieldName'])
                ->setType($propertyType);

            $method->getParameter($property['fieldName'])
                ->setNullable($property['nullable']);

            $method->addBody('$clone = clone $this;')
                ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
                ->addBody('return $clone;');

            $class->addMember($method);
        }

        return $clone;
    }

    private function withProperties(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $properties = $clone->fileDefinition->configuration()->traitProperties();

        foreach ($properties as $property) {
            $class->addMember($this->defineProperty($property));
        }

        return $clone;
    }

    private function defineProperty(array $propertyDatas): Property
    {
        $type = $propertyDatas['type'];
        $fieldNameRaw = $propertyDatas['fieldName'];
        $nullable = $propertyDatas['nullable'];

        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, implode(', ', array_keys($this->correspondingTypes())), $type)
        );

        $propertyType = $this->correspondingTypes()[$type];

        $fieldName = u($fieldNameRaw)->camel()->toString();

        $property = new Property($fieldName);
        $property->setPrivate()->setType($propertyType)->setNullable($nullable);

        if ($nullable) {
            $property->setValue(null);
        }

        return $property;
    }

    private function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
        ];
    }
}
