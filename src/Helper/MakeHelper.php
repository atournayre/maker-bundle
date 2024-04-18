<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class MakeHelper
{
    public static function allowedTypes(): array
    {
        return [
            'string',
            'integer',
            'float',
            'boolean',
            'datetime',
        ];
    }

    public static function findFilesInDirectory(string|array $directory): array
    {
        $directories = array_filter(
            is_array($directory) ? $directory : [$directory],
            fn($directory) => (new Filesystem())->exists($directory)
        );

        if ([] === $directories) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($directories)
            ->name('*.php')
            ->sortByName();

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }
        return $files;
    }
    public static function fieldDefaultType(string $fieldName): string
    {
        $defaultType = 'string';
        // try to guess the type by the field name prefix/suffix
        // convert to snake case for simplicity
        $snakeCasedField = Str::asSnakeCase($fieldName);

        if ('_at' === $suffix = substr($snakeCasedField, -3)) {
            $defaultType = 'datetime';
        } elseif ('_id' === $suffix) {
            $defaultType = 'integer';
        } elseif (0 === strpos($snakeCasedField, 'is_')) {
            $defaultType = 'boolean';
        } elseif (0 === strpos($snakeCasedField, 'has_')) {
            $defaultType = 'boolean';
        }

        return $defaultType;
    }

    public static function configureDependencies(DependencyBuilder $dependencyBuilder, array $dependencies): void
    {
        foreach ($dependencies as $class => $package) {
            $dependencyBuilder->addClassDependency($class, $package);
        }
    }
}
