<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\TraitForEntityBuilder;
use Atournayre\Bundle\MakerBundle\Builder\TraitForObjectBuilder;
use Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;

class TraitBuilderTest extends BuilderTestCase
{
    private static function propertiesAllowedTypes(): array
    {
        return [
            'string' => 'string',
            'int' => 'int',
            'float' => 'float',
            'bool' => 'bool',
            '\DateTimeInterface' => '\DateTimeInterface',
            'App\Type\Primitive\StringType' => 'App\Type\Primitive\StringType',
        ];
    }

    private static function properties(): array
    {
        return [
            PropertyDefinition::fromArray([
                'fieldName' => 'fixtureVo',
                'type' => 'App\Type\Primitive\StringType',
                'nullable' => true,
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'createdAt',
                'type' => '\DateTimeInterface',
                'nullable' => false,
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'id',
                'type' => 'int',
                'nullable' => false,
            ], __DIR__, 'App'),
        ];
    }

    public static function dataProviderForObject(): array
    {
        return [
            'Trait' => [
                'make-trait/DummyTrait.php',
                'App\Trait',
                'Dummy',
                TraitForObjectMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Trait',
                    className: 'Dummy',
                )
                    ->withProperties(self::properties())
                    ->withEnableApiPlatform(false)
                    ->withPropertiesAllowedTypes(self::propertiesAllowedTypes())
                ,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForObject
     * @covers \Atournayre\Bundle\MakerBundle\Builder\TraitForObjectBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\TraitForObjectBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildTraitForObjectOfSourceCode(string $haystackFilePath, string $namespace, string $className, TraitForObjectMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new TraitForObjectBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }

    public static function dataProviderForEntity(): array
    {
        return [
            'Trait' => [
                'make-trait/DummyEntityTrait.php',
                'App\Trait\Entity',
                'Dummy',
                TraitForEntityMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Trait\Entity',
                    className: 'Dummy',
                )
                    ->withIsUsedByEntity()
                    ->withProperties(self::properties())
                    ->withEnableApiPlatform(false)
                    ->withPropertiesAllowedTypes(self::propertiesAllowedTypes())
                ,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEntity
     * @covers \Atournayre\Bundle\MakerBundle\Builder\TraitForEntityBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\TraitForEntityBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildTraitForEntityOfSourceCode(string $haystackFilePath, string $namespace, string $className, TraitForEntityMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new TraitForEntityBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
