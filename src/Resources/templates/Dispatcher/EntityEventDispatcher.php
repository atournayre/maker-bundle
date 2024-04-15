<?php
declare(strict_types=1);

namespace App\Dispatcher;

use App\Collection\EventCollection;
use App\Contracts\Event\EntityEventDispatcherInterface;
use App\Contracts\HasContextInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Factory\ContextFactory;
use App\VO\Context;
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
        Context $context = null,
        ?string $type = null,
    ): void
    {
        $context ??= $this->contextFactory->create();

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
}
