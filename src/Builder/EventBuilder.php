<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

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
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $eventProperties = $makerConfiguration->properties();

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                \Symfony\Contracts\EventDispatcher\Event::class,
                \Webmozart\Assert\Assert::class,
                ContextTrait::class,
                ContextInterface::class,
            ])
            ->setExtends(\Symfony\Contracts\EventDispatcher\Event::class)
            ->setTraits([ContextTrait::class])
            ->setMethods([
                $this->constructor($eventProperties, $makerConfiguration),
                $this->namedConstructor($eventProperties, $makerConfiguration),
            ])
        ;
    }

    /**
     * @param PropertyDefinition[] $properties
     * @param EventMakerConfiguration $makerConfiguration
     * @return Method
     */
    private function constructor(array $properties, EventMakerConfiguration $makerConfiguration): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($properties as $property) {
            $method->addPromotedParameter($property->fieldName)
                ->setPublic()
                ->setReadOnly()
                ->setType(self::correspondingTypes($makerConfiguration)[$property->type])
            ;
        }

        return $method;
    }

    /**
     * @param PropertyDefinition[] $eventProperties
     * @param EventMakerConfiguration $makerConfiguration
     * @return Method
     */
    private function namedConstructor(array $eventProperties, EventMakerConfiguration $makerConfiguration): Method
    {
        $method = new Method('create');
        $method->setStatic()
            ->setPublic()
            ->setReturnType('self')
        ;

        $properties = array_merge(
            $eventProperties,
            [['fieldName' => 'context', 'type' => Str::absolutePathFromNamespace(ContextInterface::class, $makerConfiguration->rootNamespace(), $makerConfiguration->rootDir())]]
        );

        foreach ($properties as $property) {
            $method->addParameter($property->fieldName)
                ->setType(self::correspondingTypes($makerConfiguration)[$property->type])
            ;
        }

        $fieldNames = array_map(fn(PropertyDefinition $property) => $property->fieldName, $eventProperties);
        $selfContent = implode(', $', $fieldNames);

        $method->addBody('// Add assertions');
        $method->addBody('');
        $method->addBody('return (new self(' . ($selfContent ? '$'.$selfContent : '') . '))');
        $method->addBody('    ->withContext($context)');
        $method->addBody(';');

        return $method;
    }
}
