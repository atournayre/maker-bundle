<?php
declare(strict_types=1);

namespace App\Contracts\Event;

use App\Collection\EventCollection;
use App\Contracts\VO\ContextInterface;

interface EntityEventDispatcherInterface
{
    public function dispatch(EventCollection $eventCollection, ?ContextInterface $context = null, ?string $type = null): void;
}
