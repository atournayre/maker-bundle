<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class VOBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'VO',
        string $name = ''
    ): FileDefinitionBuilder
    {
        if (null === $config->voRelatedToAnEntity()) {
            return self::buildSimple($config, $namespace, $name);
        }

        return self::buildForEntity($config, $namespace, $name);
    }

    public static function buildSimple(
        MakerConfig $config,
        string $namespace = 'VO',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        self::addFileComment($fileDefinition->file);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
        ;

        self::constructor($class, $config->voProperties());
        self::namedConstructor($class, $config->voProperties());

        foreach ($config->voProperties() as $property) {
            self::defineProperty($class, $property);
            self::defineGetter($class, $property);
            self::defineWither($class, $property);
        }

        return $fileDefinition;
    }

    public static function buildForEntity(
        MakerConfig $config,
        string $namespace = 'VO\\Entity',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);
        $name .= 'Entity';
        $namespace .= '\\Entity';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        self::addFileComment($fileDefinition->file);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
        ;

        $entityNamespace = u($config->voRelatedToAnEntity())->ensureStart($config->rootNamespace().'\\')->prepend('\\')->toString();
        self::namedConstructorFromEntity($class, $entityNamespace, $config->voProperties());

        foreach ($config->voProperties() as $property) {
            self::defineProperty($class, $property);
            self::defineGetter($class, $property);
        }

        return $fileDefinition;
    }

    private static function addFileComment(PhpFile $file): void
    {
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
            $file->addComment($line);
        }
    }

    private static function cleanName(string $name): string
    {
        return u($name)->trimSuffix('VO')->toString();
    }

    private static function constructor(ClassType $class, array $properties): void
    {
        $method = $class->addMethod('__construct')
            ->setPrivate();

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']]);
        }

        foreach ($properties as $property) {
            $method->addBody('$this->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';');
        }
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

    private static function namedConstructor(ClassType $class, array $properties): void
    {
        $namespace = $class->getNamespace();
        $namespace->addUse(Assert::class);

        $method = $class->addMethod('create')
            ->setStatic()
            ->setPublic()
            ->setReturnType('self');

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']]);
        }

        $selfContent = implode(', $', array_column($properties, 'fieldName'));

        foreach ($properties as $property) {
            $method->addBody('Assert::' . $property['type'] . '($' . $property['fieldName'] . ');');
        }

        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent ? '$'.$selfContent : '') . ');');
    }

    private static function namedConstructorFromEntity(ClassType $class, string $entityNamespace, array $properties): void
    {
        $namespace = $class->getNamespace();
        $namespace->addUse(Assert::class);
        $namespace->addUse($entityNamespace);

        $method = $class->addMethod('create')
            ->setStatic()
            ->setPublic()
            ->setReturnType('self');

        $entityName = u($entityNamespace)->afterLast('\\')->camel()->toString();

        $class->getMethod('create')
            ->addParameter($entityName)
            ->setType($entityNamespace);

        $method->addBody('// Add assertions here if needed');
        $method->addBody('$self = new self();');

        foreach ($properties as $property) {
            $line = u($property['fieldName'])
                ->title()
                ->prepend('->get')
                ->prepend(' = $' . $entityName)
                ->prepend($property['fieldName'])
                ->prepend('// $self->')
                ->append('();')
                ->toString();

            $method->addBody($line);
        }

        $method->addBody('return $self;');
    }

    private static function defineProperty(ClassType $class, array $property): void
    {
        Assert::inArray(
            $property['type'],
            array_keys(self::correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $property['fieldName'], implode(', ', array_keys(self::correspondingTypes())), $property['type'])
        );

        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        $class->addProperty($fieldName)
            ->setPrivate()
            ->setType($propertyType);
    }

    private static function defineGetter(ClassType $class, array $property): void
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        $class->addMethod($fieldName)
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    private static function defineWither(ClassType $class, array $property): void
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        $with = u($fieldName)->title()->prepend('with')->camel()->toString();

        $class->addMethod($with)
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($property['fieldName'])
            ->setType($propertyType);

        $class->getMethod($with)
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
            ->addBody('return $clone;');
    }

    public static function buildContext(
        MakerConfig $config,
        string $namespace = 'VO',
        string $name = 'Context'
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        self::addFileComment($fileDefinition->file);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();

        $namespace1 = $class->getNamespace();
        $namespace1->addUse(\App\Contracts\Security\UserInterface::class);
        $namespace1->addUse(\App\VO\DateTime::class);

        $class->addMethod('__construct')
            ->setPrivate()
            ->addPromotedParameter('user')
            ->setPrivate()
            ->setType(\App\Contracts\Security\UserInterface::class);

        $class->getMethod('__construct')
            ->addPromotedParameter('createdAt')
            ->setPrivate()
            ->setType(\App\VO\DateTime::class);

        $class->addMethod('create')
            ->setStatic()
            ->setPublic()
            ->addComment('@throws \Exception')
            ->setReturnType('self')
            ->addParameter('user')
            ->setType(\App\Contracts\Security\UserInterface::class);

        $class->getMethod('create')
            ->addParameter('createdAt')
            ->setType('\DateTimeInterface');

        $class->getMethod('create')
            ->addBody('return new self($user, DateTime::fromInterface($createdAt));');

        $properties = [
            [
                'fieldName' => 'user',
                'type' => \App\Contracts\Security\UserInterface::class,
            ],
            [
                'fieldName' => 'createdAt',
                'type' => \App\VO\DateTime::class,
            ],
        ];

        foreach ($properties as $property) {

            $fieldName = u($property['fieldName'])->camel()->toString();

            $class->addMethod($fieldName)
                ->setPublic()
                ->setReturnType($property['type'])
                ->setBody('return $this->' . $property['fieldName'] . ';');
        }

        return $fileDefinition;
    }

    public static function buildDatetime(
        MakerConfig $config,
        string $namespace = 'VO',
        string $name = 'DateTime'
    ): FileDefinitionBuilder
    {
        $name = self::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        self::addFileComment($fileDefinition->file);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();

        $class->addMethod('__construct')
            ->setPrivate()
            ->addPromotedParameter('datetime')
            ->setPrivate()
            ->setType('\DateTimeInterface');

        $class->addMethod('fromInterface')
            ->setStatic()
            ->setPublic()
            ->setReturnType('self')
            ->addComment('@throws \Exception')
            ->addBody('return new self($datetime);');

        $class->getMethod('fromInterface')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');

        $class->addMethod('isBefore')
            ->setReturnType('bool')
            ->setBody('return $this->datetime < $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');


        $class->addMethod('isAfter')
            ->setReturnType('bool')
            ->setBody('return $this->datetime > $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');


        $class->addMethod('isBetween')
            ->setReturnType('bool')
            ->setBody('return $this->datetime > $datetime1 && $this->datetime < $datetime2;')
            ->addParameter('datetime1')
            ->setType('\DateTimeInterface');

        $class->getMethod('isBetween')
            ->addParameter('datetime2')
            ->setType('\DateTimeInterface');

        $class->addMethod('isBeforeOrEqual')
            ->setReturnType('bool')
            ->setBody('return $this->datetime <= $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');


        $class->addMethod('isAfterOrEqual')
            ->setReturnType('bool')
            ->setBody('return $this->datetime >= $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');


        $class->addMethod('isBetweenOrEqual')
            ->setReturnType('bool')
            ->setBody('return $this->datetime >= $datetime1 && $this->datetime <= $datetime2;')
            ->addParameter('datetime1')
            ->setType('\DateTimeInterface');

        $class->getMethod('isBetweenOrEqual')
            ->addParameter('datetime2')
            ->setType('\DateTimeInterface');

        $class->addMethod('toDateTime')
            ->setReturnType('\DateTimeInterface')
            ->setBody('return $this->datetime;');

        return $fileDefinition;
    }
}
