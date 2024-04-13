<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class VoForObjectBuilder extends AbstractBuilder
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
            ->withUse(\Webmozart\Assert\Assert::class)
            ->withComment()
            ->withConstructor()
            ->withNamedConstructor()
            ->withProperties()
            ->withGetters()
            ->withWithers()
        ;
    }

    private function withConstructor(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $method = new Method('__construct');
        $method->setPrivate();

        $properties = $clone->fileDefinition->configuration()->voProperties();

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType($this->correspondingTypes()[$property['type']])
            ;
            $method->addBody('$this->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';');
        }

        $class->addMember($method);

        return $clone;
    }

    public function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
        ];
    }

    private function withNamedConstructor(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        $properties = $clone->fileDefinition->configuration()->voProperties();

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType($this->correspondingTypes()[$property['type']])
            ;
        }

        $selfContent = implode(', $', array_column($properties, 'fieldName'));

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent ? '$'.$selfContent : '') . ');');

        $class->addMember($method);

        return $clone;
    }

    private function withProperties(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $properties = $clone->fileDefinition->configuration()->voProperties();

        foreach ($properties as $property) {
            $class->addMember($this->defineProperty($property));
        }

        return $clone;
    }

    private function defineProperty(array $property): Property
    {
        $type = $property['type'];
        $fieldNameRaw = $property['fieldName'];

        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes()),
            Str::sprintf('Property "%s" should be of type %s; %s given', $fieldNameRaw, Str::implode(', ', array_keys($this->correspondingTypes())), $type)
        );

        $propertyType = $this->correspondingTypes()[$type];

        $fieldName = Str::property($fieldNameRaw);

        $property = new Property($fieldName);
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    private function withGetters(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $properties = $clone->fileDefinition->configuration()->voProperties();

        foreach ($properties as $property) {
            $class->addMember($this->defineGetter($property));
        }

        return $clone;
    }

    private function defineGetter(array $property): Method
    {
        $propertyType = $this->correspondingTypes()[$property['type']];

        return (new Method(Str::property($property['fieldName'])))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    private function withWithers(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $properties = $clone->fileDefinition->configuration()->voProperties();

        foreach ($properties as $property) {
            $class->addMember($this->defineWither($property));
        }

        return $clone;
    }

    private function defineWither(array $property): Method
    {
        $propertyType = $this->correspondingTypes()[$property['type']];

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

    private function withComment(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $comment = [
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

        foreach ($comment as $line) {
            $class->addComment($line);
        }

        return $clone;
    }
}
