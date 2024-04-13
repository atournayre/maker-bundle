<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

class Str
{
    public static function classNameFromNamespace(string $namespace, ?string $suffix): string
    {
        return UStr::classNameFromNamespace($namespace, $suffix)->toString();
    }

    public static function absolutePathFromNamespace(string $namespace, string $rootNamespace, string $rootDirectory, ?string $extension = '.php'): string
    {
        return UStr::absolutePathFromNamespace($namespace, $rootNamespace, $rootDirectory, $extension)->toString();
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

    public static function sprintf(string $format, ...$args): string
    {
        return UStr::sprintf($format, ...$args)->toString();
    }

    public static function implode(string $glue, array $pieces): string
    {
        return UStr::implode($glue, $pieces)->toString();
    }

    public static function replace(string $search, string $replace, string $subject): string
    {
        return UStr::replace($search, $replace, $subject)->toString();
    }
}
