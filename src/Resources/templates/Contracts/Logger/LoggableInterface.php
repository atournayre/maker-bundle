<?php
declare(strict_types=1);

namespace App\Contracts\Logger;

interface LoggableInterface
{
    public function toLog(): array;
}
