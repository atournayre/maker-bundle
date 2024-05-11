<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\CommandBuilder;
use Atournayre\Bundle\MakerBundle\Config\CommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class CommandBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        return [
            'Command with suffix' => [
                'make-command/DummyCommand.php',
                'App\Command',
                'DummyCommand',
                CommandMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Command',
                    className: 'DummyCommand',
                )
                    ->withTitle('I am a dummy command')
                    ->withDescription('Run me to see what happens')
                    ->withCommandName('app:dummy:command')
            ],
            'Command without suffix' => [
                'make-command/DummyCommand.php',
                'App\Command',
                'Dummy',
                CommandMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Command',
                    className: 'Dummy',
                )
                    ->withTitle('I am a dummy command')
                    ->withDescription('Run me to see what happens')
                    ->withCommandName('app:dummy:command')
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\CommandBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\CommandBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\CommandMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildCommandOfSourceCode(string $haystackFilePath, string $namespace, string $className, CommandMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new CommandBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
