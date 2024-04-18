<?php
declare(strict_types=1);

namespace App\Contracts\Event;

use Symfony\Contracts\EventDispatcher\Event;

interface HasEventsInterface
{
    public function events(?string $type = null): array;

	public function addEvent(Event $event): void;

	public function setEvent(string $index, Event $event): void;

	public function removeEvent(Event $event): void;
}
