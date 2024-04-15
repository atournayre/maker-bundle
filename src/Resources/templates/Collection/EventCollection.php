<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Collection\TypedCollection;
use Symfony\Contracts\EventDispatcher\Event;

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
            ->filter(fn (Event $event) => $event instanceof $type)
            ->toArray();
    }
}
