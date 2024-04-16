<?php
declare(strict_types=1);

namespace App\Dispatcher;

use App\Collection\EventCollection;
use App\Contracts\Event\EntityEventDispatcherInterface;
use App\Contracts\HasContextInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\VO\ContextInterface;
use App\Factory\ContextFactory;
use App\VO\Null\NullContext;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class EntityEventDispatcher implements EntityEventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface          $logger,
        private readonly ContextFactory           $contextFactory,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function dispatch(
        EventCollection $eventCollection,
        ?ContextInterface $context = null,
        ?string $type = null,
    ): void
    {
        $context = $this->context($context);

        $events = $eventCollection->events($type);

        Assert::allIsInstanceOf($events, HasContextInterface::class);

        foreach ($events as $event) {
            $eventName = get_class($event);

            $this->logger->info(sprintf('Adding context to %s event', $eventName));
            $event = $event->withContext($context);

            $this->logger->info(sprintf('Dispatching %s event', $eventName));
            $this->eventDispatcher->dispatch($event);

            $this->logger->info(sprintf('Event %s dispatched', $eventName));
        }
    }

    /**
     * @throws \Exception
     */
    private function context(?ContextInterface $context = null): ContextInterface
    {
        if ($context instanceof NullContext) {
            return $context;
        }

        if (null === $context) {
            return $this->contextFactory->create();
        }

        return $context;
    }
}
