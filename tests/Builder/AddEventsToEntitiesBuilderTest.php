<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\AddEventsToEntityBuilder;
use Atournayre\Bundle\MakerBundle\Config\AddEventsToEntitiesMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Symfony\Component\Finder\SplFileInfo;

class AddEventsToEntitiesBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $entityNamespace = 'Atournayre\Bundle\MakerBundle\Tests\fixtures';

        $entityWithoutEventsWithoutConstructPath = __DIR__.'/../fixtures/FixtureEntityWithoutEventsWithoutConstruct.php';
        $entityWithoutEventsWithoutConstruct = new SplFileInfo($entityWithoutEventsWithoutConstructPath, $entityWithoutEventsWithoutConstructPath, $entityWithoutEventsWithoutConstructPath);

        $entityWithoutEventsWithNotEmptyConstructPath = __DIR__.'/../fixtures/FixtureEntityWithoutEventsWithNotEmptyConstruct.php';
        $entityWithoutEventsWithNotEmptyConstruct = new SplFileInfo($entityWithoutEventsWithNotEmptyConstructPath, $entityWithoutEventsWithNotEmptyConstructPath, $entityWithoutEventsWithNotEmptyConstructPath);

        return [
            'AddEventsToEntities without construct' => [
                'make-add-events-to-entities/DummyAddEventsToEntitiesWithConstruct.php',
                $entityNamespace,
                'Dummy',
                AddEventsToEntitiesMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: $entityNamespace,
                    className: 'Dummy',
                )
                    ->withSourceCode($entityWithoutEventsWithoutConstruct->getContents())
            ],
            'AddEventsToEntities with construct' => [
                'make-add-events-to-entities/DummyAddEventsToEntitiesWithNotEmptyConstruct.php',
                $entityNamespace,
                'Dummy',
                AddEventsToEntitiesMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: $entityNamespace,
                    className: 'Dummy',
                )
                    ->withSourceCode($entityWithoutEventsWithNotEmptyConstruct->getContents())
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AddEventsToEntitiesBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AddEventsToEntitiesBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\AddEventsToEntitiesMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildAddEventsToEntitiesOfSourceCode(string $haystackFilePath, string $namespace, string $className, AddEventsToEntitiesMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new AddEventsToEntityBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
