<?php
declare(strict_types=1);

namespace App\Trait\Entity;

trait TimestampableEntityTrait
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
}
