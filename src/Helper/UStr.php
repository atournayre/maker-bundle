<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Helper;

use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

class UStr
{
    public static function classNameFromNamespace(string $namespace, ?string $suffix): UnicodeString
    {
        return u($namespace)
            ->afterLast('\\')
            ->ensureEnd($suffix ?? '');
    }

    public static function absolutePathFromNamespace(string $namespace, string $rootNamespace, string $rootDirectory, ?string $extension = null): UnicodeString
    {
        return u($namespace)
            ->replace($rootNamespace, $rootDirectory)
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append($extension ?? '.php');
    }

    public static function wither(string $name): UnicodeString
    {
        return u($name)->lower()->camel()->title()->prepend('with');
    }

    public static function getter(string $name): UnicodeString
    {
        return u($name)->lower()->camel()->title()->prepend('get');
    }

    public static function setter(string $name): UnicodeString
    {
        return u($name)->lower()->camel()->title()->prepend('set');
    }

    public static function property(string $name): UnicodeString
    {
        return u($name)->lower()->camel();
    }

    public static function prefixByRootNamespace(string $string, string $rootNamespace): UnicodeString
    {
        return u($string)
            ->ensureStart($rootNamespace.'\\');
    }

    public static function classNameSemiColonFromNamespace(string $namespace): UnicodeString
    {
        return u($namespace)->afterLast('\\')->append('::class');
    }

    public static function namespaceFromPath(string $path, string $rootDir): UnicodeString
    {
        return u($path)
            ->afterLast($rootDir)
            ->beforeLast('.')
            ->trimPrefix('/')
            ->replace('/', '\\');
    }

    public static function trimNamespaceEnd(string $namespace, string $suffix): UnicodeString
    {
        return u($namespace)->trimEnd($suffix);
    }

    public static function asSnakeCase(string $string): UnicodeString
    {
        return u($string)->snake();
    }

    public static function asCamelCase(string $string): UnicodeString
    {
        return u($string)->camel();
    }

    public static function sprintf(string $format, ...$args): UnicodeString
    {
        return u(sprintf($format, ...$args));
    }

    public static function implode(string $glue, array $pieces): UnicodeString
    {
        return u(implode($glue, $pieces));
    }

    public static function replace(string $string, string $from, string $to): UnicodeString
    {
        return u($string)->replace($from, $to);
    }

    public static function namespaceWithoutClassname(string $string): UnicodeString
    {
        return u($string)->beforeLast('\\');
    }
}
