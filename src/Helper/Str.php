<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use Aimeos\Map;
use Symfony\Component\String\AbstractString;

class Str
{
    public static function classNameFromNamespace(string $namespace, ?string $suffix): string
    {
        return UStr::classNameFromNamespace($namespace, $suffix)->toString();
    }

    public static function absolutePathFromNamespace(string $namespace, string $rootNamespace, string $rootDirectory, ?string $extension = '.php'): string
    {
        $normalizedNamespace = Map::from(UStr::create($namespace)->split('\\'))
            ->map(fn(AbstractString $part) => $part->title()->toString())
            ->join('\\')
        ;

        return UStr::absolutePathFromNamespace($normalizedNamespace, $rootNamespace, $rootDirectory, $extension)->toString();
    }

    public static function wither(string $name): string
    {
        return UStr::wither($name)->toString();
    }

    public static function getter(string $name): string
    {
        return UStr::getter($name)->toString();
    }

    public static function setter(string $name): string
    {
        return UStr::setter($name)->toString();
    }

    public static function property(string $name): string
    {
        return UStr::property($name)->toString();
    }

    public static function prefixByRootNamespace(string $string, string $rootNamespace): string
    {
        return UStr::prefixByRootNamespace($string, $rootNamespace)->toString();
    }

    public static function classNameSemiColonFromNamespace(string $namespace): string
    {
        return UStr::classNameSemiColonFromNamespace($namespace)->toString();
    }

    public static function namespaceFromPath(string $path, string $rootDir): string
    {
        return UStr::namespaceFromPath($path, $rootDir)->toString();
    }

    public static function trimNamespaceEnd(string $namespace, string $suffix): string
    {
        return UStr::trimNamespaceEnd($namespace, $suffix)->toString();
    }

    public static function asSnakeCase(string $string): string
    {
        return UStr::asSnakeCase($string)->toString();
    }

    public static function asCamelCase(string $string): string
    {
        return UStr::asCamelCase($string)->toString();
    }

    /**
     * @param string $format
     * @param string ...$args
     * @return string
     */
    public static function sprintf(string $format, ...$args): string
    {
        return UStr::sprintf($format, ...$args)->toString();
    }

    /**
     * @param string $glue
     * @param array<string> $pieces
     * @return string
     */
    public static function implode(string $glue, array $pieces): string
    {
        return UStr::implode($glue, $pieces)->toString();
    }

    public static function replace(string $string, string $from, string $to): string
    {
        return UStr::replace($string, $from, $to)->toString();
    }

    public static function namespaceWithoutClassname(string $string): string
    {
        return UStr::namespaceWithoutClassname($string)->toString();
    }

    public static function cleanNamespace(string $namespace): string
    {
        return UStr::cleanNamespace($namespace)->toString();
    }

    public static function startsWith(string $classname, string $string): bool
    {
        return UStr::startsWith($classname, $string);
    }
}
