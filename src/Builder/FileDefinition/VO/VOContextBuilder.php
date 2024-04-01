<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VOBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class VOContextBuilder implements FileDefinitionBuilderInterface
{
    private static array $PROPERTIES = [
        [
            'fieldName' => 'user',
            'type' => \App\Contracts\Security\UserInterface::class,
        ],
        [
            'fieldName' => 'createdAt',
            'type' => \App\VO\DateTime::class,
        ],
    ];

    public static function build(
        MakerConfig $config,
        string      $namespace = 'VO',
        string      $name = 'Context'
    ): FileDefinitionBuilder
    {
        $name = VOBuilder::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        VOBuilder::addFileComment($fileDefinition->file);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addMember(self::construct())
            ->addMember(self::create())
        ;

        foreach (self::$PROPERTIES as $property) {
            $class->addMember(self::propertyAccessor($property));
        }

        $class->getNamespace()
            ->addUse(\App\Contracts\Security\UserInterface::class)
            ->addUse(\App\VO\DateTime::class)
        ;

        return $fileDefinition;
    }

    private static function construct(): Method
    {
        $method = new Method('__construct');
        $method
            ->setPrivate()
            ->addPromotedParameter('user')
            ->setPrivate()
            ->setType(\App\Contracts\Security\UserInterface::class);

        $method
            ->addPromotedParameter('createdAt')
            ->setPrivate()
            ->setType(\App\VO\DateTime::class);
        return $method;
    }

    private static function create()
    {
        $method = new Method('create');
        $method
            ->setStatic()
            ->setPublic()
            ->addComment('@throws \Exception')
            ->setReturnType('self')
            ->addParameter('user')
            ->setType(\App\Contracts\Security\UserInterface::class);

        $method
            ->addParameter('createdAt')
            ->setType('\DateTimeInterface');

        $method
            ->addBody('return new self($user, DateTime::fromInterface($createdAt));');
        return $method;
    }

    private static function propertyAccessor(array $property): Method
    {
        $method = new Method($property['fieldName']);
        $method
            ->setPublic()
            ->setReturnType($property['type'])
            ->setBody('return $this->' . $property['fieldName'] . ';');
        return $method;
    }
}
