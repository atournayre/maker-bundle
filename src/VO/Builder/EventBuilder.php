<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\VO\ContextInterface;
use App\Trait\ContextTrait;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;

class EventBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $eventProperties = $fileDefinition->configuration()->getExtraProperty('eventProperties') ?? [];

        return (new self($fileDefinition))
            ->createFile()
            ->extends(\Symfony\Contracts\EventDispatcher\Event::class)
            ->withUse(\Symfony\Contracts\EventDispatcher\Event::class)
            ->withUse(\Webmozart\Assert\Assert::class)
            ->withUse(ContextTrait::class)
            ->withUse(ContextInterface::class)
            ->addTrait(ContextTrait::class)
            ->addMember(self::constructor($eventProperties))
            ->addMember(self::namedConstructor($eventProperties))
        ;
    }

    private static function constructor(array $voProperties): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($voProperties as $property) {
            $method->addPromotedParameter($property['fieldName'])
                ->setPublic()
                ->setReadOnly()
                ->setType(self::correspondingTypes()[$property['type']])
            ;
        }

        return $method;
    }

    private static function correspondingTypes(): array
    {
        return [
            'string' => 'string',
            'integer' => 'int',
            'float' => 'float',
            'boolean' => 'bool',
            'datetime' => '\DateTimeInterface',
            'context' => ContextInterface::class,
        ];
    }

    private static function namedConstructor(array $eventProperties): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        $properties = array_merge(
            $eventProperties,
            [['fieldName' => 'context', 'type' => 'context']]
        );

        foreach ($properties as $property) {
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes()[$property['type']])
            ;
        }

        $selfContent = implode(', $', array_column($eventProperties, 'fieldName'));

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return (new self(' . ($selfContent ? '$'.$selfContent : '') . '))');
        $method->addBody('    ->withContext($context)');
        $method->addBody(';');

        return $method;
    }
}