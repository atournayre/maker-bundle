<?php
declare(strict_types=1);

namespace App\Contracts\Null;

interface NullableInterface
{
    public function isNull(): bool;
}
