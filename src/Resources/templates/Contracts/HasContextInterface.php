<?php
declare(strict_types=1);

namespace App\Contracts;

interface HasContextInterface
{
    public function hasContext(): bool;
}
