<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\EnumBuilder;
use Atournayre\Bundle\MakerBundle\Config\EnumMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\CaseDefinition;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class EnumBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $casesForInt = [
            CaseDefinition::fromArray([
                'name' => 'ACTIVE',
                'value' => '1',
            ]),
        ];
        $casesForString = [
            CaseDefinition::fromArray([
                'name' => 'ACTIVE',
                'value' => 'Active',
            ]),
        ];
        $casesForPure = [
            CaseDefinition::fromArray([
                'name' => 'ACTIVE',
                'value' => null,
            ]),
        ];

        return [
            'Backed Enum int' => [
                'make-enum/BooleanEnum.php',
                'App\Enum',
                'BooleanEnum',
                EnumMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Enum',
                    className: 'BooleanEnum',
                )
                    ->withCases($casesForInt),
            ],
            'Backed Enum int without suffix' => [
                'make-enum/BooleanEnum.php',
                'App\Enum',
                'Boolean',
                EnumMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Enum',
                    className: 'Boolean',
                )
                    ->withCases($casesForInt),
            ],
            'Backed Enum string' => [
                'make-enum/StatusWithValueEnum.php',
                'App\Enum',
                'StatusWithValueEnum',
                EnumMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Enum',
                    className: 'StatusWithValueEnum',
                )
                    ->withCases($casesForString),
            ],
            'Pure Enum' => [
                'make-enum/StatusEnum.php',
                'App\Enum',
                'StatusEnum',
                EnumMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Enum',
                    className: 'StatusEnum',
                )
                    ->withCases($casesForPure),
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\EnumBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\EnumBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\EnumMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildEnumOfSourceCode(string $haystackFilePath, string $namespace, string $className, EnumMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new EnumBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
