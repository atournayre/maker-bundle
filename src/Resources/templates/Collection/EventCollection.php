<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Collection\TypedCollection;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @extends TypedCollection<Event>
 *
 * @method SplFileInfoCollection add(Event $value)
 * @method Event[] values()
 * @method Event first()
 * @method Event last()
 */
final class EventCollection extends TypedCollection
{
    protected static string $type = Event::class;

    public function events(?string $type = null): array
    {
        if (null === $type) {
            return $this->values();
        }

        return $this
            ->toMap()
            ->filter(static fn(Event $event): bool => $event instanceof $type)
            ->toArray();
    }
}
