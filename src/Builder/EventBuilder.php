<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use App\Contracts\VO\ContextInterface;
use App\Trait\ContextTrait;
use Atournayre\Bundle\MakerBundle\Config\EventMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class EventBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === EventMakerConfiguration::class;
    }

    /**
     * @param EventMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $eventProperties = $makerConfiguration->properties();

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                Event::class,
                Assert::class,
                ContextTrait::class,
                ContextInterface::class,
            ])
            ->setExtends(Event::class)
            ->setTraits([ContextTrait::class])
            ->setMethods([
                $this->constructor($eventProperties, $makerConfiguration),
                $this->namedConstructor($eventProperties, $makerConfiguration),
            ])
        ;
    }

    /**
     * @param PropertyDefinition[] $properties
     */
    private function constructor(array $properties, EventMakerConfiguration $eventMakerConfiguration): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($properties as $property) {
            $method->addPromotedParameter($property->fieldName)
                ->setPublic()
                ->setReadOnly()
                ->setType(self::correspondingTypes($eventMakerConfiguration)[$property->type])
            ;
        }

        return $method;
    }

    /**
     * @param PropertyDefinition[] $eventProperties
     */
    private function namedConstructor(array $eventProperties, EventMakerConfiguration $eventMakerConfiguration): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        $properties = array_merge(
            $eventProperties,
            [['fieldName' => 'context', 'type' => Str::absolutePathFromNamespace(ContextInterface::class, $eventMakerConfiguration->rootNamespace(), $eventMakerConfiguration->rootDir())]]
        );

        foreach ($properties as $property) {
            $method->addParameter($property->fieldName)
                ->setType(self::correspondingTypes($eventMakerConfiguration)[$property->type])
            ;
        }

        $fieldNames = array_map(static fn(PropertyDefinition $property): string => $property->fieldName, $eventProperties);
        $selfContent = implode(', $', $fieldNames);

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return (new self(' . ($selfContent !== '' && $selfContent !== '0' ? '$'.$selfContent : '') . '))');
        $method->addBody('    ->withContext($context)');
        $method->addBody(';');

        return $method;
    }
}
