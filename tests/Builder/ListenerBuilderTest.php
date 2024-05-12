<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\ListenerBuilder;
use Atournayre\Bundle\MakerBundle\Config\ListenerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class ListenerBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $propertiesAllowedTypes = [
            'string' => 'string',
            'int' => 'int',
            'float' => 'float',
            'bool' => 'bool',
            '\DateTimeInterface' => '\DateTimeInterface',
            'App\Type\Primitive\StringType' => 'App\Type\Primitive\StringType',
        ];

        return [
            'Listener' => [
                'make-listener/DummyListener.php',
                'App\EventListener',
                'dummy',
                ListenerMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\EventListener',
                    className: 'dummy',
                )
            ->withEventNamespace('App\Event\DummyEvent')
            ->withPropertiesAllowedTypes($propertiesAllowedTypes)
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ListenerBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ListenerBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\ListenerMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildListenerOfSourceCode(string $haystackFilePath, string $namespace, string $className, ListenerMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new ListenerBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
