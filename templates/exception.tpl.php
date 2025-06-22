<?php

declare(strict_types=1);

namespace {{ namespace }};

use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Common\Exception\ThrowableTrait;

class {{ class_name }} extends \{{ parent_exception }} implements ThrowableInterface
{
    use ThrowableTrait;
}
