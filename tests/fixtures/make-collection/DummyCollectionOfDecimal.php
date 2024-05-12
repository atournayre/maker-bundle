<?php

/**
 * @extends DecimalValueCollection<DecimalValue>
 *
 * @method DummyCollection add(DecimalValue $value)
 * @method DecimalValue[] values()
 * @method DecimalValue first()
 * @method DecimalValue last()
 * @method DecimalValue offsetGet(mixed $offset)
 * @method DummyCollection offsetSet(mixed $offset, DecimalValue $value)
 */

declare(strict_types=1);

namespace App\Collection;

use Atournayre\Collection\DecimalValueCollection;
use Atournayre\Types\DecimalValue;

final class DummyCollection extends DecimalValueCollection
{
    protected static string $type = DecimalValue::class;
}
