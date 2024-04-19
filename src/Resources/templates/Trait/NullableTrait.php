<?php
declare(strict_types=1);

namespace App\Trait;

trait NullableTrait
{
    public function isNull(): bool
    {
        return true;
    }
}
