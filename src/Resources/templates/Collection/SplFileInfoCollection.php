<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Collection\TypedCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class SplFileInfoCollection extends TypedCollection
{
    protected static string $type = SplFileInfo::class;

    public static function fromFinder(Finder $finder): self
    {
        $files = iterator_to_array($finder);
        return self::createAsMap($files);
    }
}
