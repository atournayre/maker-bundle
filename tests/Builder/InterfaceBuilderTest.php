<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class InterfaceBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'Interface with suffix' => [
                'make-interface/DummyInterface.php',
                'App\Contracts',
                'DummyInterface',
                InterfaceMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Contracts',
                    className: 'DummyInterface',
                )
            ],
            'Interface without suffix' => [
                'make-interface/DummyInterface.php',
                'App\Contracts',
                'Dummy',
                InterfaceMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Contracts',
                    className: 'Dummy',
                )
            ],
            'Interface without suffix lower case' => [
                'make-interface/DummyInterface.php',
                'App\Contracts',
                'dummy',
                InterfaceMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Contracts',
                    className: 'dummy',
                )
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildInterfaceOfSourceCode(string $haystackFilePath, string $namespace, string $className, InterfaceMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new InterfaceBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
