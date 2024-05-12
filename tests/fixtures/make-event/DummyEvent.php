<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Event;

use App\Contracts\VO\ContextInterface;
use App\Trait\ContextTrait;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;

final class DummyEvent extends Event
{
    use ContextTrait;

    private function __construct(
        public readonly string $id,
    ) {
    }

    public static function create(string $id, ContextInterface $context): self
    {
        // Add assertions

        return (new self($id))
            ->withContext($context)
        ;
    }
}
