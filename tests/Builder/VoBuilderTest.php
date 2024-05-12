<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\VoForEntityBuilder;
use Atournayre\Bundle\MakerBundle\Builder\VoForObjectBuilder;
use Atournayre\Bundle\MakerBundle\Config\VoForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\VoForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureEntity;

class VoBuilderTest extends BuilderTestCase
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
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'createdAt',
                'type' => '\DateTimeInterface',
            ], __DIR__, 'App'),
            PropertyDefinition::fromArray([
                'fieldName' => 'id',
                'type' => 'int',
            ], __DIR__, 'App'),
        ];
    }

    public static function dataProviderForObject(): array
    {
        return [
            'Vo' => [
                'make-vo/DummyVo.php',
                'App\VO',
                'Dummy',
                VoForObjectMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\VO',
                    className: 'Dummy',
                )
                    ->withProperties(self::properties())
                    ->withPropertiesAllowedTypes(self::propertiesAllowedTypes())
                ,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForObject
     * @covers \Atournayre\Bundle\MakerBundle\Builder\VoForObjectBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\VoForObjectBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\VoForObjectMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildVoForObjectOfSourceCode(string $haystackFilePath, string $namespace, string $className, VoForObjectMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new VoForObjectBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }

    public static function dataProviderForEntity(): array
    {
        return [
            'Vo' => [
                'make-vo/DummyEntityVo.php',
                'App\VO\Entity',
                'Dummy',
                VoForEntityMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\VO\Entity',
                    className: 'Dummy',
                )
                    ->withRelatedEntity(FixtureEntity::class)
                    ->withProperties(self::properties())
                    ->withPropertiesAllowedTypes(self::propertiesAllowedTypes())
                ,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEntity
     * @covers \Atournayre\Bundle\MakerBundle\Builder\VoForEntityBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\VoForEntityBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\VoForEntityMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildVoForEntityOfSourceCode(string $haystackFilePath, string $namespace, string $className, VoForEntityMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new VoForEntityBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
