<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\EventBuilder;
use Atournayre\Bundle\MakerBundle\Config\EventMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class EventBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $properties = [
            PropertyDefinition::fromArray([
                'fieldName' => 'id',
                'type' => 'string',
                'nullable' => false,
            ], __DIR__, 'App'),
        ];

        $propertiesAllowedTypes = [
            'string' => 'string',
            'int' => 'int',
            'float' => 'float',
            'bool' => 'bool',
            '\DateTimeInterface' => '\DateTimeInterface',
            'App\Type\Primitive\StringType' => 'App\Type\Primitive\StringType',
            'App\Contracts\VO\ContextInterface' => 'App\Contracts\VO\ContextInterface',
        ];

        return [
            'Event' => [
                'make-event/DummyEvent.php',
                'App\Event',
                'dummy',
                EventMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Event',
                    className: 'dummy',
                )
            ->withProperties($properties)
            ->withPropertiesAllowedTypes($propertiesAllowedTypes)
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\EventBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\EventBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\EventMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildEventOfSourceCode(string $haystackFilePath, string $namespace, string $className, EventMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new EventBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
