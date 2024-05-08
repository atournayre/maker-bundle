<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\ListenerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

final class ListenerBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ListenerMakerConfiguration::class;
    }

    /**
     * @param ListenerMakerConfiguration $makerConfiguration
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $eventNamespace = $makerConfiguration->eventNamespace();

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([$eventNamespace])
            ->setAttributes($this->attributes($makerConfiguration))
            ->setMethods([
                $this->invoke($makerConfiguration),
            ])
        ;
    }

    /**
     * @param ListenerMakerConfiguration $makerConfiguration
     * @return array<Attribute>
     */
    private function attributes(ListenerMakerConfiguration $makerConfiguration): array
    {
        $eventNamespace = $makerConfiguration->eventNamespace();
        $eventName = Str::classNameFromNamespace($eventNamespace, 'Event');

        return [
            new Attribute(\Symfony\Component\EventDispatcher\Attribute\AsEventListener::class, [
                'event' => new Literal(Str::classNameSemiColonFromNamespace($eventName)),
            ]),
        ];
    }

    private function invoke(ListenerMakerConfiguration $makerConfiguration): Method
    {
        $eventNamespace = $makerConfiguration->eventNamespace();

        $method = new Method('__invoke');
        $method->addParameter('event')
            ->setType($eventNamespace);
        $method->setPublic()->setReturnType('void');

        return $method;
    }
}
