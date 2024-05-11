<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\Config\ServiceCommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;

class ServiceCommandBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'ServiceCommand' => [
                'make-service/DummyCommandService.php',
                'App\Service\Command',
                'DummyCommandService',
                ServiceCommandMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Service\Command',
                    className: 'DummyCommandService',
                )
                    ->withVo(FixtureVo::class)
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ServiceCommandBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ServiceCommandBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\ServiceCommandMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildServiceCommandOfSourceCode(string $haystackFilePath, string $namespace, string $className, ServiceCommandMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new ServiceCommandBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
