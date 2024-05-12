<?php
declare(strict_types=1);

namespace App\Trait;

use App\Collection\EventCollection;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Add to constructor:
 * $this->events = EventCollection::createAsList([]);
 * or
 * $this->events = EventCollection::createAsMap([]);
 */
trait EventsTrait
{
    private EventCollection $events;

    public function events(?string $type = null): array
    {
        return $this->events->events($type);
    }

    public function addEvent(Event $event): void
    {
        $this->events[] = $event;
    }

    public function setEvent(string $index, Event $event): void
    {
        $this->events[$index] = $event;
    }

    public function removeEvent(Event $event): void
    {
        $index = $this->events->toMap()->search($event);

        if (null === $index) {
            return;
        }

        $this->events->offsetUnset($index);
    }
}
