<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class TraitForEntityBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();

        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->addTrait($fileDefinition->fullName());

        $trait = (new self($fileDefinition))
            ->withFile($file)
            ->withUse(\Doctrine\ORM\Mapping::class, 'ORM');

        $traitProperties = $config->traitProperties();

        $nullableProperties = array_filter($traitProperties, fn($property) => !$property['nullable']);
        if (!empty($nullableProperties)) {
            $trait = $trait->withUse(\Webmozart\Assert\Assert::class);
        }

        $dateTimeInterfaceProperties = array_filter($traitProperties, fn($property) => $property['type'] === '\DateTimeInterface');
        if (!empty($dateTimeInterfaceProperties)) {
            $trait = $trait->withUse(\Doctrine\DBAL\Types\Types::class);
        }

        return $trait
            ->withProperties()
            ->withPropertyGettersForEntity()
            ->withPropertySettersForEntity();
    }

    private function withUse(string $classname, ?string $alias = null): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse($classname, $alias);

        return $clone;
    }

    private function withPropertySettersForEntity(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $traitProperties = $clone->fileDefinition->configuration()->traitProperties();

        foreach ($traitProperties as $property) {
            $fieldName = u($property['fieldName'])->camel()->toString();
            $set = u($fieldName)->title()->prepend('set')->camel()->toString();

            $method = new Method($set);
            $method->setPublic()
                ->setReturnType('self')
                ->addParameter($property['fieldName'])
                ->setType($property['type']);

            $method->getParameter($property['fieldName'])
                ->setNullable();

            $method->addBody('$this->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
                ->addBody('return $this;');

            $class->addMember($method);
        }

        return $clone;
    }

    private function withPropertyGettersForEntity(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $traitProperties = $clone->fileDefinition->configuration()->traitProperties();

        foreach ($traitProperties as $property) {
            if (!$property['nullable']) {
                $fieldName = u($property['fieldName'])->camel()->toString();
                $method = new Method($fieldName);
                $method->setPublic()
                    ->setReturnType($property['type'])
                    ->setReturnNullable($property['nullable']);

                if (!$property['nullable']) {
                    $method->addBody('Assert::notNull($this->' . $property['fieldName'] . ');');
                }

                $method->addBody('return $this->' . $property['fieldName'] . ';');

                $class->addMember($method);
            }

            $fieldName = u($property['fieldName'])->camel()->title()->prepend('get')->toString();
            $method = new Method($fieldName);
            $method->setPublic()
                ->setReturnType($property['type'])
                ->setReturnNullable()
                ->setBody('return $this->' . $property['fieldName'] . ';');

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

        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, implode(', ', array_keys($this->correspondingTypes())), $type)
        );

        $propertyType = $this->correspondingTypes()[$type];

        $fieldName = u($fieldNameRaw)->camel()->toString();

        $property = new Property($fieldName);
        $property->setPrivate()
            ->setType($propertyType)
            ->setNullable()
            ->setValue(null)
        ;

        return $this->propertyUsedByEntity($property);
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

    private function propertyUsedByEntity(Property $property): Property
    {
        $clone = clone $property;

        $columnArgs = [
            'nullable' => true, // By default, all properties are nullable, not to break schema
        ];

        $propertyType = $this->doctrineCorrespondingTypes()[$clone->getType()];
        if (null !== $propertyType) {
            $columnArgs['type'] = $propertyType;
        }

        $clone->addAttribute(\Doctrine\ORM\Mapping\Column::class, $columnArgs);

        return $clone;
    }

    private function doctrineCorrespondingTypes(): array
    {
        return [
            'string' => null,
            'int' => null,
            'float' => null,
            'bool' => null,
            '\DateTimeInterface' => class_exists(\Doctrine\DBAL\Types\Types::class) ? \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE : null,
        ];
    }
}
