<?php
declare(strict_types=1);

namespace App\Contracts\Event;

use App\Collection\EventCollection;
use App\VO\Context;

interface EntityEventDispatcherInterface
{
    public function dispatch(EventCollection $eventCollection, Context $context = null, ?string $type = null): void;
}
