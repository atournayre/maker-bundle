<?php
declare(strict_types=1);

namespace App\Trait;

trait NotNullableTrait
{
    public function isNull(): bool
    {
        return false;
    }
}
