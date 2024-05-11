<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\CollectionBuilder;
use Atournayre\Bundle\MakerBundle\Config\CollectionMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;

class CollectionBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'Collection of decimal' => [
                'make-collection/DummyCollectionOfDecimal.php',
                'App\Collection',
                'DummyCollection',
                CollectionMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Collection',
                    className: 'DummyCollection',
                )
                    ->withOfDecimals()
            ],
            'Collection of object' => [
                'make-collection/DummyCollectionOfFixtureVo.php',
                'App\Collection',
                'DummyCollection',
                CollectionMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Collection',
                    className: 'DummyCollection',
                )
                    ->withRelatedObject(FixtureVo::class)
            ],
            'Collection of object immutable' => [
                'make-collection/DummyCollectionOfFixtureVoImmutable.php',
                'App\Collection',
                'DummyCollection',
                CollectionMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Collection',
                    className: 'DummyCollection',
                )
                    ->withIsImmutable(true)
                    ->withRelatedObject(FixtureVo::class)
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\CollectionBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\CollectionBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\CollectionMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildCollectionOfSourceCode(string $haystackFilePath, string $namespace, string $className, CollectionMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new CollectionBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
