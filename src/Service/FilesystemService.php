<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class FilesystemService
{
    /**
     * @param string|array<string> $includedDirectory
     * @param string|array<string>|null $excludedDirectory
     * @return string[]
     */
    public function findFilesInDirectory(
        string|array $includedDirectory,
        string|array|null $excludedDirectory = null
    ): array
    {
        $directories = array_filter(
            is_array($includedDirectory) ? $includedDirectory : [$includedDirectory],
            static fn($directory): bool => (new Filesystem())->exists($directory)
        );

        if ([] === $directories) {
            return [];
        }

        if (null !== $excludedDirectory) {
            $excludedDirectories = array_filter(
                is_array($excludedDirectory) ? $excludedDirectory : [$excludedDirectory],
                static fn($directory): bool => (new Filesystem())->exists($directory)
            );
        }

        $finder = (new Finder())
            ->files()
            ->in($directories)
            ->exclude($excludedDirectories ?? [])
            ->name('*.php')
            ->sortByName();

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $files;
    }

}
