<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
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

        return VOForEntityBuilder::build($config, $namespace, $name);
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
            ->addMember(self::constructor($config->voProperties()))
            ->addMember(self::namedConstructor($config->voProperties()))
        ;

        foreach ($config->voProperties() as $property) {
            $class
                ->addMember(self::defineProperty($property))
                ->addMember(self::defineGetter($property))
                ->addMember(self::defineWither($property));
        }

        $class->getNamespace()
            ->addUse(Assert::class)
        ;

        return $fileDefinition;
    }

    public static function addFileComment(PhpFile $file): void
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

    public static function cleanName(string $name): string
    {
        return u($name)->trimSuffix('VO')->toString();
    }

    private static function constructor(array $properties): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($properties as $property) {
            $method
                ->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']]);
        }

        foreach ($properties as $property) {
            $method->addBody('$this->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';');
        }

        return $method;
    }

    public static function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
        ];
    }

    private static function namedConstructor(array $properties): Method
    {
        $method = new Method('create');
        $method->setStatic() ->setPublic() ->setReturnType('self');

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])->setType(self::correspondingTypes()[$property['type']]);
        }

        $selfContent = implode(', $', array_column($properties, 'fieldName'));

        foreach ($properties as $property) {
            $method->addBody('Assert::' . $property['type'] . '($' . $property['fieldName'] . ');');
        }

        $method->addBody('');
        $method->addBody('return new self(' . ($selfContent ? '$'.$selfContent : '') . ');');

        return $method;
    }

    public static function defineProperty(array $property): Property
    {
        Assert::inArray(
            $property['type'],
            array_keys(self::correspondingTypes()),
            sprintf('Property "%s" should be of type %s; %s given', $property['fieldName'], implode(', ', array_keys(self::correspondingTypes())), $property['type'])
        );

        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        return (new Property($fieldName))
            ->setPrivate()
            ->setType($propertyType);
    }

    public static function defineGetter(array $property): Method
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        return (new Method($fieldName))
            ->setPublic()
            ->setReturnType($propertyType)
            ->setBody('return $this->' . $property['fieldName'] . ';');
    }

    private static function defineWither(array $property): Method
    {
        $propertyType = self::correspondingTypes()[$property['type']];

        $fieldName = u($property['fieldName'])->camel()->toString();

        $with = u($fieldName)->title()->prepend('with')->camel()->toString();

        $method = new Method($with);
        $method
            ->setPublic()
            ->setReturnType('self')
            ->addParameter($property['fieldName'])
            ->setType($propertyType);

        $method
            ->addBody('$clone = clone $this;')
            ->addBody('$clone->' . $property['fieldName'] . ' = $' . $property['fieldName'] . ';')
            ->addBody('return $clone;');
        return $method;
    }

    public function generateSourceCode(FileDefinition $fileDefinition): string
    {
        throw new \LogicException('Not implemented');
    }
}
