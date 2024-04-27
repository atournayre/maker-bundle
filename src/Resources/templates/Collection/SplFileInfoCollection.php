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
        return self::createAsList($files);
    }

    public function filterByExtension(string $extension): self
    {
        $array = $this
            ->toMap()
            ->filter(fn(SplFileInfo $file) => $file->getExtension() === $extension)
            ->toArray();

        return self::createAsList($array);
    }

    public function filterBySize(int $size): self
    {
        $array = $this
            ->toMap()
            ->filter(fn(SplFileInfo $file) => $file->getSize() === $size)
            ->toArray();

        return self::createAsList($array);
    }

    public function filterByContent(string $content): self
    {
        $array = $this
            ->toMap()
            ->filter(fn(SplFileInfo $file) => str_contains($file->getContents(), $content))
            ->toArray();

        return self::createAsList($array);
    }

    public function totalSize(): float
    {
        return $this
            ->toMap()
            ->map(fn(SplFileInfo $file) => $file->getSize())
            ->sum();
    }


}
