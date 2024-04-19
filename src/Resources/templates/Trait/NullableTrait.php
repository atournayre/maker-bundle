<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Resources\templates\Trait;

trait NullableTrait
{
    public function isNull(): bool
    {
        return true;
    }
}
