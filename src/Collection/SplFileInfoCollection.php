<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Atournayre\Collection\TypedCollection;
use Symfony\Component\Finder\SplFileInfo;

class SplFileInfoCollection extends TypedCollection
{
    protected static string $type = SplFileInfo::class;
}
