<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

class ListenerBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $eventNamespace = $fileDefinition->configuration()->getExtraProperty('eventNamespace');
        $eventName = Str::classNameFromNamespace($eventNamespace, 'Event');

        $attributes = [
            new Attribute(\Symfony\Component\EventDispatcher\Attribute\AsEventListener::class, [
                'event' => new Literal(Str::classNameSemiColonFromNamespace($eventName)),
            ]),
        ];

        return (new self($fileDefinition))
            ->createFile()
            ->withUse($eventNamespace)
            ->setAttributes($attributes)
            ->addMember(self::invoke($fileDefinition))
        ;
    }

    private static function invoke(FileDefinition $fileDefinition): Method
    {
        $eventNamespace = $fileDefinition->configuration()->getExtraProperty('eventNamespace');

        $method = new Method('__invoke');
        $method->addParameter('event')
            ->setType($eventNamespace);
        $method->setPublic()->setReturnType('void');

        return $method;
    }
}
