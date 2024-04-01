<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class VOForEntityBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'VO\\Entity',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = VOBuilder::cleanName($name);
        $name .= 'Entity';
        $namespace .= '\\Entity';
        $entityNamespace = u($config->voRelatedToAnEntity())->ensureStart($config->rootNamespace().'\\')->prepend('\\')->toString();

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        VOBuilder::addFileComment($fileDefinition->file);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->addMember(self::namedConstructorFromEntity($entityNamespace, $config->voProperties()))
        ;

        foreach ($config->voProperties() as $property) {
            $class->addMember(VOBuilder::defineProperty($property));
            $class->addMember(VOBuilder::defineGetter($property));
        }

        $class->getNamespace()
            ->addUse(Assert::class)
            ->addUse($entityNamespace)
        ;

        return $fileDefinition;
    }

    private static function namedConstructorFromEntity(string $entityNamespace, array $properties): Method
    {
        $method = new Method('create');
        $method->setStatic() ->setPublic()->setReturnType('self');

        $entityName = u($entityNamespace)->afterLast('\\')->camel()->toString();

        $method->addParameter($entityName)->setType($entityNamespace);
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
        return $method;
    }
}
