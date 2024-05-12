<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Atournayre\Collection\TypedCollection;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @extends TypedCollection<SplFileInfo>
 *
 * @method SplFileInfoCollection add(SplFileInfo $value)
 * @method SplFileInfo[] values()
 * @method SplFileInfo first()
 * @method SplFileInfo last()
 */
class SplFileInfoCollection extends TypedCollection
{
    protected static string $type = SplFileInfo::class;
}
