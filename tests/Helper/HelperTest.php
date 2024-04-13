<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Helper;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public static function dataProviderClassNameFromNamespace(): array
    {
        return [
            ['App\DTO\Dummy', '', 'Dummy'],
            ['App\DTO\Sub\Dummy', '', 'Dummy'],
        ];
    }

    /**
     * @dataProvider dataProviderClassNameFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::classNameFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::classNameFromNamespace
     * @param string $namespace
     * @param string $suffix
     * @param string $expected
     * @return void
     */
    public function testClassNameFromNamespace(string $namespace, string $suffix, string $expected): void
    {
        self::assertEquals($expected, Str::classNameFromNamespace($namespace, $suffix));
    }

    public static function dataProviderAbsolutePathFromNamespace(): array
    {
        return [
            ['App\DTO\Dummy', 'App', '/srv/app/src', null, '/srv/app/src/DTO/Dummy.php'],
            ['App\DTO\Dummy', 'App', '/srv/app/src', '.php', '/srv/app/src/DTO/Dummy.php'],
            ['App\DTO\Sub\Dummy', 'App', '/srv/app/src', '.php', '/srv/app/src/DTO/Sub/Dummy.php'],
        ];
    }

    /**
     * @dataProvider dataProviderAbsolutePathFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::absolutePathFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::absolutePathFromNamespace
     * @param string $namespace
     * @param string $rootNamespace
     * @param string $rootDirectory
     * @param string|null $extension
     * @param string $expected
     * @return void
     */
    public function testAbsolutePathFromNamespace(string $namespace, string $rootNamespace, string $rootDirectory, ?string $extension, string $expected): void
    {
        self::assertEquals($expected, Str::absolutePathFromNamespace($namespace, $rootNamespace, $rootDirectory, $extension));
    }

    public static function dataProviderWither(): array
    {
        return [
            ['name', 'withName'],
            ['long_name', 'withLongName'],
            ['long-name', 'withLongName'],
            ['long name', 'withLongName'],
            ['long Name', 'withLongName'],
            ['LonG NaMe', 'withLongName'],
        ];
    }

    /**
     * @dataProvider dataProviderWither
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::wither
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::wither
     * @param string $name
     * @param string $expected
     * @return void
     */
    public function testWither(string $name, string $expected): void
    {
        self::assertEquals($expected, Str::wither($name));
    }

    public static function dataProviderGetter(): array
    {
        return [
            ['name', 'getName'],
            ['long_name', 'getLongName'],
            ['long-name', 'getLongName'],
            ['long name', 'getLongName'],
            ['long Name', 'getLongName'],
            ['LonG NaMe', 'getLongName'],
        ];
    }

    /**
     * @dataProvider dataProviderGetter
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::getter
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::getter
     * @param string $name
     * @param string $expected
     * @return void
     */
    public function testGetter(string $name, string $expected): void
    {
        self::assertEquals($expected, Str::getter($name));
    }

    public static function dataProviderSetter(): array
    {
        return [
            ['name', 'setName'],
            ['long_name', 'setLongName'],
            ['long-name', 'setLongName'],
            ['long name', 'setLongName'],
            ['long Name', 'setLongName'],
            ['LonG NaMe', 'setLongName'],
        ];
    }

    /**
     * @dataProvider dataProviderSetter
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::setter
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::setter
     * @param string $name
     * @param string $expected
     * @return void
     */
    public function testSetter(string $name, string $expected): void
    {
        self::assertEquals($expected, Str::setter($name));
    }

    public static function dataProviderProperty(): array
    {
        return [
            ['name', 'name'],
            ['long_name', 'longName'],
            ['long-name', 'longName'],
            ['long name', 'longName'],
            ['long Name', 'longName'],
            ['LonG NaMe', 'longName'],
        ];
    }

    /**
     * @dataProvider dataProviderProperty
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::property
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::property
     * @param string $name
     * @param string $expected
     * @return void
     */
    public function testProperty(string $name, string $expected): void
    {
        self::assertEquals($expected, Str::property($name));
    }

    public static function dataProviderPrefixByRootNamespace(): array
    {
        return [
            ['App\DTO\Dummy', 'App', 'App\DTO\Dummy'],
            ['App\DTO\Sub\Dummy', 'App', 'App\DTO\Sub\Dummy'],
            ['DTO\Sub\Dummy', 'App', 'App\DTO\Sub\Dummy'],
        ];
    }

    /**
     * @dataProvider dataProviderPrefixByRootNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::prefixByRootNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::prefixByRootNamespace
     * @param string $string
     * @param string $rootNamespace
     * @param string $expected
     * @return void
     */
    public function testPrefixByRootNamespace(string $string, string $rootNamespace, string $expected): void
    {
        self::assertEquals($expected, Str::prefixByRootNamespace($string, $rootNamespace));
    }

    public static function dataProviderClassNameSemiColonFromNamespace(): array
    {
        return [
            ['App\DTO\Dummy', 'Dummy::class'],
            ['App\DTO\Sub\Dummy', 'Dummy::class'],
        ];
    }

    /**
     * @dataProvider dataProviderClassNameSemiColonFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::classNameSemiColonFromNamespace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::classNameSemiColonFromNamespace
     * @param string $namespace
     * @param string $expected
     * @return void
     */
    public function testClassNameSemiColonFromNamespace(string $namespace, string $expected): void
    {
        self::assertEquals($expected, Str::classNameSemiColonFromNamespace($namespace));
    }

    public static function dataProviderNamespaceFromPath(): array
    {
        return [
            ['/srv/app/src/App/DTO/Dummy.php', '/srv/app/src', 'App\DTO\Dummy'],
            ['/srv/app/src/App/DTO/Sub/Dummy.php', '/srv/app/src', 'App\DTO\Sub\Dummy'],
        ];
    }

    /**
     * @dataProvider dataProviderNamespaceFromPath
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::namespaceFromPath
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::namespaceFromPath
     * @param string $path
     * @param string $rootDir
     * @param string $expected
     * @return void
     */
    public function testNamespaceFromPath(string $path, string $rootDir, string $expected): void
    {
        self::assertEquals($expected, Str::namespaceFromPath($path, $rootDir));
    }

    public static function dataProviderTrimNamespaceEnd(): array
    {
        return [
            ['App\DTO\DummyDTO', 'DTO', 'App\DTO\Dummy'],
            ['App\DTO\Sub\DummyDTO', 'DTO', 'App\DTO\Sub\Dummy'],
        ];
    }

    /**
     * @dataProvider dataProviderTrimNamespaceEnd
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::trimNamespaceEnd
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::trimNamespaceEnd
     * @param string $namespace
     * @param string $suffix
     * @param string $expected
     * @return void
     */
    public function testTrimNamespaceEnd(string $namespace, string $suffix, string $expected): void
    {
        self::assertEquals($expected, Str::trimNamespaceEnd($namespace, $suffix));
    }

    public static function dataProviderAsSnakeCase(): array
    {
        return [
            ['App\DTO\Dummy', 'app_dto_dummy'],
            ['App\DTO\Sub\Dummy', 'app_dto_sub_dummy'],
        ];
    }

    /**
     * @dataProvider dataProviderAsSnakeCase
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::asSnakeCase
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::asSnakeCase
     * @param string $string
     * @param string $expected
     * @return void
     */
    public function testAsSnakeCase(string $string, string $expected): void
    {
        self::assertEquals($expected, Str::asSnakeCase($string));
    }

    public static function dataProviderAsCamelCase(): array
    {
        return [
            ['App\DTO\Dummy', 'appDTODummy'],
            ['App\DTO\Sub\Dummy', 'appDTOSubDummy'],
        ];
    }

    /**
     * @dataProvider dataProviderAsCamelCase
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::asCamelCase
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::asCamelCase
     * @param string $string
     * @param string $expected
     * @return void
     */
    public function testAsCamelCase(string $string, string $expected): void
    {
        self::assertEquals($expected, Str::asCamelCase($string));
    }

    public static function dataProviderSprintf(): array
    {
        return [
            ['%s %s', ['Hello', 'World'], 'Hello World'],
            ['%s %s', ['Hello', 'World', 'Extra'], 'Hello World'],
        ];
    }

    /**
     * @dataProvider dataProviderSprintf
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::sprintf
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::sprintf
     * @param string $format
     * @param array $args
     * @param string $expected
     * @return void
     */
    public function testSprintf(string $format, array $args, string $expected): void
    {
        self::assertEquals($expected, Str::sprintf($format, ...$args));
    }

    public static function dataProviderImplode(): array
    {
        return [
            [' ', ['Hello', 'World'], 'Hello World'],
            [' ', ['Hello', 'World', 'Extra'], 'Hello World Extra'],
        ];
    }

    /**
     * @dataProvider dataProviderImplode
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::implode
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::implode
     * @param string $glue
     * @param array $pieces
     * @param string $expected
     * @return void
     */
    public function testImplode(string $glue, array $pieces, string $expected): void
    {
        self::assertEquals($expected, Str::implode($glue, $pieces));
    }

    public static function dataProviderReplace(): array
    {
        return [
            ['Hello World', 'World', 'Universe', 'Hello Universe'],
            ['Hello World', 'World', 'Universe', 'Hello Universe'],
        ];
    }

    /**
     * @dataProvider dataProviderReplace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\Str::replace
     * @covers       \Atournayre\Bundle\MakerBundle\Helper\UStr::replace
     * @param string $string
     * @param string $from
     * @param string $to
     * @param string $expected
     * @return void
     */
    public function testReplace(string $string, string $from, string $to, string $expected): void
    {
        self::assertEquals($expected, Str::replace($string, $from, $to));
    }
}
