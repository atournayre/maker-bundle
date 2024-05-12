<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\DtoBuilder;
use Atournayre\Bundle\MakerBundle\Config\DtoMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class DtoBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $properties = [
            PropertyDefinition::fromArray([
                'fieldName' => 'id',
                'type' => 'int',
                'nullable' => false,
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'name',
                'type' => 'string',
                'nullable' => true,
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'date',
                'type' => '\DateTimeInterface',
                'nullable' => true,
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'fixtureVo',
                'type' => 'App\Type\Primitive\StringType',
                'nullable' => true,
            ], __DIR__, 'App'),
        ];

        $propertiesAllowedTypes = [
            'string' => 'string',
            'int' => 'int',
            'float' => 'float',
            'bool' => 'bool',
            '\DateTimeInterface' => '\DateTimeInterface',
            'App\Type\Primitive\StringType' => 'App\Type\Primitive\StringType',
        ];

        return [
            'DTO with properties' => [
                'make-dto/DummyDtoWithProperties.php',
                'App\DTO',
                'Dummy',
                DtoMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\DTO',
                    className: 'Dummy',
                )
                    ->withProperties($properties)
                    ->withPropertiesAllowedTypes($propertiesAllowedTypes),
            ],
            'DTO without properties' => [
                'make-dto/DummyDtoWithoutProperties.php',
                'App\DTO',
                'Dummy',
                DtoMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\DTO',
                    className: 'Dummy',
                )
                    ->withProperties([])
                    ->withPropertiesAllowedTypes($propertiesAllowedTypes),
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\DtoBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\DtoBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\DtoMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildDtoOfSourceCode(string $haystackFilePath, string $namespace, string $className, DtoMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new DtoBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
