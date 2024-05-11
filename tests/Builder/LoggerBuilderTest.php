<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\LoggerBuilder;
use Atournayre\Bundle\MakerBundle\Config\LoggerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class LoggerBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'Logger' => [
                'make-logger/DummyLogger.php',
                'App\Logger',
                'dummy',
                LoggerMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Logger',
                    className: 'dummy',
                )
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\LoggerBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\LoggerBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\LoggerMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildLoggerOfSourceCode(string $haystackFilePath, string $namespace, string $className, LoggerMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new LoggerBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
