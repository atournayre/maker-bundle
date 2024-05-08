<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Collection\EventCollection;
use App\Contracts\Event\HasEventsInterface;
use App\Trait\EventsTrait;
use Atournayre\Bundle\MakerBundle\Config\AddEventsToEntitiesMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

class AddEventsToEntityBuilder extends FromTemplateBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === AddEventsToEntitiesMakerConfiguration::class;
    }

    /**
     * @param AddEventsToEntitiesMakerConfiguration $makerConfiguration
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $phpFileDefinition = parent::createPhpFileDefinition($makerConfiguration);

        $phpFileDefinition->addUse(HasEventsInterface::class);
        $phpFileDefinition->addUse(EventCollection::class);
        $phpFileDefinition->addTrait(EventsTrait::class);
        $phpFileDefinition->addImplement(HasEventsInterface::class);

        if ($phpFileDefinition->hasMethod('__construct')) {
            return $phpFileDefinition
                ->updateMethod('__construct', $this->updateConstructorMethod($phpFileDefinition->getMethod('__construct')));
        }

        return $phpFileDefinition
            ->addMethod($this->createConstructorMethod());
    }

    private function createConstructorMethod(): Method
    {
        return (new Method('__construct'))
            ->setPublic()
            ->setBody($this->bodyContent());
    }

    private function updateConstructorMethod(Method $method): Method
    {
        $bodyContainsEventCollection = str_contains($method->getBody(), 'EventCollection::createAsList');

        if ($bodyContainsEventCollection) {
            return $method;
        }

        $method->addBody(PHP_EOL.$this->bodyContent());
        return $method;
    }

    private function bodyContent(): string
    {
        return '$this->events = EventCollection::createAsList([]);';
    }
}
