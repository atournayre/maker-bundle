<?php
declare(strict_types=1);

namespace App\Trait\Entity;

trait BlameableEntityTrait
{
    use CreatedByTrait;
    use UpdatedByTrait;
}
