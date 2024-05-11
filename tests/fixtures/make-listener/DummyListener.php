<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\EventListener;

use App\Event\DummyEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: DummyEvent::class)]
final class DummyListener
{
    public function __invoke(DummyEvent $event): void
    {
    }
}
