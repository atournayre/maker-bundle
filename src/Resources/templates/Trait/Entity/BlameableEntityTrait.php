<?php
declare(strict_types=1);

namespace App\Entity\Traits;

trait BlameableEntityTrait
{
    use CreatedByTrait;
    use UpdatedByTrait;
}
