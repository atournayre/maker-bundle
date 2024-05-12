<?php

/**
 * @extends TypedCollectionImmutable<FixtureVo>
 *
 * @method DummyCollection add(FixtureVo $value)
 * @method FixtureVo[] values()
 * @method FixtureVo first()
 * @method FixtureVo last()
 * @method FixtureVo offsetGet(mixed $offset)
 * @method DummyCollection offsetSet(mixed $offset, FixtureVo $value)
 */

declare(strict_types=1);

namespace App\Collection;

use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;
use Atournayre\Collection\TypedCollectionImmutable;

final class DummyCollection extends TypedCollectionImmutable
{
    protected static string $type = FixtureVo::class;
}
