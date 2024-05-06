<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use App\Trait\NotNullableTrait;
use App\Trait\NullableTrait;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class MakeHelper
{
    /**
     * @deprecated
     * @param string|array<string> $includedDirectory
     * @param string|array<string>|null $excludedDirectory
     * @return string[]
     */
    public static function findFilesInDirectory(
        string|array $includedDirectory,
        string|array|null $excludedDirectory = null
    ): array
    {
        $directories = array_filter(
            is_array($includedDirectory) ? $includedDirectory : [$includedDirectory],
            fn($directory) => (new Filesystem())->exists($directory)
        );

        if ([] === $directories) {
            return [];
        }

        if (null !== $excludedDirectory) {
            $excludedDirectories = array_filter(
                is_array($excludedDirectory) ? $excludedDirectory : [$excludedDirectory],
                fn($directory) => (new Filesystem())->exists($directory)
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

    /**
     * @param DependencyBuilder $dependencyBuilder
     * @param array<string, string> $dependencies
     * @return void
     */
    public static function configureDependencies(DependencyBuilder $dependencyBuilder, array $dependencies): void
    {
        foreach ($dependencies as $class => $package) {
            $dependencyBuilder->addClassDependency($class, $package);
        }
    }

    public static function nullableTrait(FileDefinition $fileDefinition): string
    {
        if (Str::startsWith($fileDefinition->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
