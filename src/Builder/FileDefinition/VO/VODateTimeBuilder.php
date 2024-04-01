<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class VODateTimeBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'VO',
        string      $name = 'DateTime'
    ): FileDefinitionBuilder
    {
        $name = VOBuilder::cleanName($name);

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        VOBuilder::addFileComment($fileDefinition->file);

        $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addMember(self::construct())
            ->addMember(self::fromInterface())
            ->addMember(self::isBefore())
            ->addMember(self::isAfter())
            ->addMember(self::isBetween())
            ->addMember(self::isBeforeOrEqual())
            ->addMember(self::isAfterOrEqual())
            ->addMember(self::isBetweenOrEqual())
            ->addMember(self::toDateTime())
        ;

        return $fileDefinition;
    }

    private static function construct(): Method
    {
        $method = new Method('__construct');
        $method
            ->setPrivate()
            ->addPromotedParameter('datetime')
            ->setPrivate()
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function fromInterface(): Method
    {
        $method = new Method('fromInterface');
        $method
            ->setStatic()
            ->setPublic()
            ->setReturnType('self')
            ->setBody('return new self($datetime);')
            ->addComment('@throws \Exception');
        $method
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isBefore(): Method
    {
        $method = new Method('isBefore');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime < $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isAfter(): Method
    {
        $method = new Method('isAfter');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime > $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isBetween(): Method
    {
        $method = new Method('isBetween');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime > $datetime1 && $this->datetime < $datetime2;')
            ->addParameter('datetime1')
            ->setType('\DateTimeInterface');
        $method
            ->addParameter('datetime2')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isBeforeOrEqual(): Method
    {
        $method = new Method('isBeforeOrEqual');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime <= $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isAfterOrEqual(): Method
    {
        $method = new Method('isAfterOrEqual');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime >= $datetime;')
            ->addParameter('datetime')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function isBetweenOrEqual(): Method
    {
        $method = new Method('isBetweenOrEqual');
        $method
            ->setReturnType('bool')
            ->setBody('return $this->datetime >= $datetime1 && $this->datetime <= $datetime2;')
            ->addParameter('datetime1')
            ->setType('\DateTimeInterface');
        $method
            ->addParameter('datetime2')
            ->setType('\DateTimeInterface');
        return $method;
    }

    private static function toDateTime(): Method
    {
        $method = new Method('toDateTime');
        $method
            ->setReturnType('\DateTimeInterface')
            ->setBody('return $this->datetime;');
        return $method;
    }
}
