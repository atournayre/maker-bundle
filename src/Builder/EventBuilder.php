<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Contracts\VO\ContextInterface;
use App\Trait\ContextTrait;
use Atournayre\Bundle\MakerBundle\Config\EventMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class EventBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === EventMakerConfiguration::class;
    }

    public function createInstance(MakerConfigurationInterface|EventMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $eventProperties = $makerConfiguration->properties();

        return parent::createInstance($makerConfiguration)
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
     * @param array{fieldName: string, type: string}[] $properties
     * @param MakerConfigurationInterface|EventMakerConfiguration $makerConfiguration
     * @return Method
     */
    private function constructor(array $properties, MakerConfigurationInterface|EventMakerConfiguration $makerConfiguration): Method
    {
        $method = new Method('__construct');
        $method->setPrivate();

        foreach ($properties as $property) {
            $method->addPromotedParameter($property['fieldName'])
                ->setPublic()
                ->setReadOnly()
                ->setType(self::correspondingTypes($makerConfiguration)[$property['type']])
            ;
        }

        return $method;
    }

    /**
     * @param array{fieldName: string, type: string}[] $eventProperties
     * @param MakerConfigurationInterface|EventMakerConfiguration $makerConfiguration
     * @return Method
     */
    private function namedConstructor(array $eventProperties, MakerConfigurationInterface|EventMakerConfiguration $makerConfiguration): Method
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
            $method->addParameter($property['fieldName'])
                ->setType(self::correspondingTypes($makerConfiguration)[$property['type']])
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
