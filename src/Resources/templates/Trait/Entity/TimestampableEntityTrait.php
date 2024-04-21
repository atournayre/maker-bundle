<?php
declare(strict_types=1);

namespace App\Entity\Traits;

trait TimestampableEntityTrait
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
}
