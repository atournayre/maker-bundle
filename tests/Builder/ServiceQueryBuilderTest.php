<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\ServiceQueryBuilder;
use Atournayre\Bundle\MakerBundle\Config\ServiceQueryMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;

class ServiceQueryBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'ServiceQuery' => [
                'make-service/DummyQueryService.php',
                'App\Service\Query',
                'DummyQueryService',
                ServiceQueryMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Service\Query',
                    className: 'DummyQueryService',
                )
                    ->withVo(FixtureVo::class)
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ServiceQueryBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ServiceQueryBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\ServiceQueryMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildServiceQueryOfSourceCode(string $haystackFilePath, string $namespace, string $className, ServiceQueryMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new ServiceQueryBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
