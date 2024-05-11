<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class InterfaceBuilderTest extends BuilderTestCase
{
    /**
     * @return void
     * @throws \Throwable
     * @covers \Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\InterfaceBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildInterfaceSourceCode(): void
    {
        $haystackFilePath = self::fixtureFilePath('make-interface/DummyInterface.php');
        $namespace = 'App\Contracts';
        $className = 'DummyInterface';

        $makerConfiguration = InterfaceMakerConfiguration::fromNamespace(
            rootDir: __DIR__,
            rootNamespace: self::rootNamespace(),
            namespace: $namespace,
            className: $className,
        );
        $builder = new InterfaceBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
