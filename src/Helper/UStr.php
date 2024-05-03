<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

class UStr
{
    public static function create(string $string): UnicodeString
    {
        return u($string);
    }

    public static function classNameFromNamespace(string $namespace, ?string $suffix): UnicodeString
    {
        return self::create($namespace)
            ->afterLast('\\')
            ->ensureEnd($suffix ?? '');
    }

    public static function absolutePathFromNamespace(string $namespace, string $rootNamespace, string $rootDirectory, ?string $extension = null): UnicodeString
    {
        return self::create($namespace)
            ->replace($rootNamespace, $rootDirectory)
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append($extension ?? '.php');
    }

    public static function wither(string $name): UnicodeString
    {
        return self::create($name)->camel()->title()->prepend('with');
    }

    public static function getter(string $name): UnicodeString
    {
        return self::create($name)->camel()->title()->prepend('get');
    }

    public static function setter(string $name): UnicodeString
    {
        return self::create($name)->camel()->title()->prepend('set');
    }

    public static function property(string $name): UnicodeString
    {
        return self::create($name)->camel();
    }

    public static function prefixByRootNamespace(string $string, string $rootNamespace): UnicodeString
    {
        return self::create($string)
            ->ensureStart($rootNamespace.'\\');
    }

    public static function classNameSemiColonFromNamespace(string $namespace): UnicodeString
    {
        return self::create($namespace)->afterLast('\\')->append('::class');
    }

    public static function namespaceFromPath(string $path, string $rootDir): UnicodeString
    {
        return self::create($path)
            ->afterLast($rootDir)
            ->beforeLast('.')
            ->trimPrefix('/')
            ->replace('/', '\\');
    }

    public static function trimNamespaceEnd(string $namespace, string $suffix): UnicodeString
    {
        return self::create($namespace)->trimEnd($suffix);
    }

    public static function asSnakeCase(string $string): UnicodeString
    {
        return self::create($string)->snake();
    }

    public static function asCamelCase(string $string): UnicodeString
    {
        return self::create($string)->camel();
    }

    public static function sprintf(string $format, ...$args): UnicodeString
    {
        return self::create(sprintf($format, ...$args));
    }

    public static function implode(string $glue, array $pieces): UnicodeString
    {
        return self::create(implode($glue, $pieces));
    }

    public static function replace(string $string, string $from, string $to): UnicodeString
    {
        return self::create($string)->replace($from, $to);
    }

    public static function namespaceWithoutClassname(string $string): UnicodeString
    {
        return self::create($string)->beforeLast('\\');
    }

    public static function cleanNamespace(string $namespace): UnicodeString
    {
        return self::create($namespace)->replace('\\\\', '\\');
    }

    public static function startsWith(string $classname, string $string): bool
    {
        return self::create($classname)->startsWith($string);
    }
}
