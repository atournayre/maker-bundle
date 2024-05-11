<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\ExceptionBuilder;
use Atournayre\Bundle\MakerBundle\Config\ExceptionMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class ExceptionBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'Exception with named constructor' => [
                'make-exception/ExceptionWithNamedConstructor.php',
                'App\Exception',
                'Dummy',
                ExceptionMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Exception',
                    className: 'Dummy',
                )
                    ->withType(\Exception::class)
                    ->withNamedConstructor('itFails')
                ,
            ],
            'Exception without named constructor' => [
                'make-exception/ExceptionWithoutNamedConstructor.php',
                'App\Exception',
                'Dummy',
                ExceptionMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Exception',
                    className: 'Dummy',
                )
                    ->withType(\Exception::class)
                ,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ExceptionBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ExceptionBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\ExceptionMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildExceptionOfSourceCode(string $haystackFilePath, string $namespace, string $className, ExceptionMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new ExceptionBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
