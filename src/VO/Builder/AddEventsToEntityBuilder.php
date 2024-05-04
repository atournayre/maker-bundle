<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Collection\EventCollection;
use App\Contracts\Event\HasEventsInterface;
use App\Trait\EventsTrait;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\TraitType;

class AddEventsToEntityBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        $fileDefinition = $fileDefinition->withSourceCodeFromAbsolutePath();

        $file = static::create($fileDefinition)
            ->createFromCode($fileDefinition->sourceCode())
            ->addImplement(HasEventsInterface::class)
            ->withUse(HasEventsInterface::class)
            ->withUse(EventCollection::class)
            ->addTrait(EventsTrait::class);

        $class = $file->getClass();

        if ($class->hasMethod('__construct')) {
            return $file
                ->updateMethod('__construct', self::updateConstructorMethod($class))
            ;
        }

        return $file
            ->addMember(self::createConstructorMethod())
        ;
    }

    private static function createConstructorMethod(): Method
    {
        return (new Method('__construct'))
            ->setPublic()
            ->setBody(self::bodyContent());
    }

    private static function updateConstructorMethod(ClassType|TraitType|InterfaceType|EnumType $class): Method
    {
        $method = $class->getMethod('__construct');

        $bodyContainsEventCollection = str_contains($method->getBody(), 'EventCollection::createAsList');

        if ($bodyContainsEventCollection) {
            return $method;
        }

        $method->addBody(self::bodyContent());
        return $method;
    }

    private static function bodyContent(): string
    {
        return '$this->events = EventCollection::createAsList([]);';
    }
}
