<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use PHPUnit\Framework\TestCase;

class BuilderTestCase extends TestCase
{
    final public static function assertFileContentEquals(string $expectedContent, string $haystackFilePath): void
    {
        self::assertFileExists($haystackFilePath);
        self::assertEquals($expectedContent, file_get_contents($haystackFilePath));
    }

    final public static function fixtureFilePath(string $string): string
    {
        return __DIR__ . '/../fixtures/' . $string;
    }

    final public static function rootNamespace(): string
    {
        return 'App';
    }
}
