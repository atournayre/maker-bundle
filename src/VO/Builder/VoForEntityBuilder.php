<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Helper\UStr;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

class VoForEntityBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $entityNamespace = self::entityNamespace($fileDefinition);

        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal();

        return (new self($fileDefinition))
            ->withFile($file)
            ->withUse(\Webmozart\Assert\Assert::class)
            ->withUse($entityNamespace->toString())
            ->withComment()
            ->withNamedConstructor()
            ->withProperties()
            ->withGetters();
    }

    private static function entityNamespace(FileDefinition $fileDefinition): UnicodeString
    {
        $config = $fileDefinition->configuration();
        return UStr::prefixByRootNamespace($config->voRelatedToAnEntity(), $config->rootNamespace());
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

        $property = new Property(Str::property($fieldNameRaw));
        $property->setPrivate()->setType($propertyType);

        return $property;
    }

    private function withNamedConstructor(): self
    {
        $properties = $this->fileDefinition->configuration()->voProperties();
        $clone = clone $this;
        $class = $clone->getClass();

        $method = new Method('create');
        $method->setStatic()->setPublic()->setReturnType('self');

        $entityNamespace = self::entityNamespace($this->fileDefinition);
        $entityName = $entityNamespace
            ->afterLast('\\')
            ->camel()
            ->toString();

        $method->addParameter($entityName)->setType($entityNamespace->toString());
        $method->addBody('// Add assertions here if needed');
        $method->addBody('$self = new self();');

        foreach ($properties as $property) {
            $linePattern = '// $self->%s = $%s->%s();';
            $line = Str::sprintf($linePattern, $property['fieldName'], $entityName, Str::getter($property['fieldName']));
            $method->addBody($line);
        }

        $method->addBody('return $self;');

        $class->addMember($method);

        return $clone;
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
