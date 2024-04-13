<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\VO;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Tests\Config\MakerConfigTestHelper;
use Atournayre\Bundle\MakerBundle\VO\Builder\AddAttributeBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\DtoBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ExceptionBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\LoggerBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceQueryBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\TraitForEntityBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\TraitForObjectBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\VoForEntityBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\VoForObjectBuilder;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Atournayre\Bundle\MakerBundle\VO\FileDefinition
 * @group Unit
 * @group FileDefinition
 */
class FileDefinitionTest extends TestCase
{
    public static function dataProvider(): \Generator
    {
        yield 'Dto' => [
            MakerConfigTestHelper::dto(),
            'App\DTO',
            'Dummy',
            '/srv/app/src/DTO/Dummy.php',
            DtoBuilder::class,
        ];

        yield 'Exception' => [
            MakerConfigTestHelper::exception(),
            'App\Exception',
            'Dummy',
            '/srv/app/src/Exception/Dummy.php',
            ExceptionBuilder::class,
        ];

        yield 'Interface' => [
            MakerConfigTestHelper::interface(),
            'App\Contracts',
            'DummyInterface',
            '/srv/app/src/Contracts/DummyInterface.php',
            InterfaceBuilder::class,
        ];

        yield 'Logger' => [
            MakerConfigTestHelper::logger(),
            'App\Logger',
            'DummyLogger',
            '/srv/app/src/Logger/DummyLogger.php',
            LoggerBuilder::class,
        ];

        yield 'CommandService' => [
            MakerConfigTestHelper::commandService(),
            'App\Service\Command',
            'DummyCommandService',
            '/srv/app/src/Service/Command/DummyCommandService.php',
            ServiceCommandBuilder::class,
        ];

        yield 'CommandServiceVOAttribute' => [
            MakerConfigTestHelper::commandServiceVOAttribute(),
            'App\VO',
            'Dummy',
            '/srv/app/src/VO/Dummy.php',
            AddAttributeBuilder::class,
        ];

        yield 'QueryService' => [
            MakerConfigTestHelper::queryService(),
            'App\Service\Query',
            'DummyQueryService',
            '/srv/app/src/Service/Query/DummyQueryService.php',
            ServiceQueryBuilder::class,
        ];

        yield 'QueryServiceVOAttribute' => [
            MakerConfigTestHelper::queryServiceVOAttribute(),
            'App\VO',
            'Dummy',
            '/srv/app/src/VO/Dummy.php',
            AddAttributeBuilder::class,
        ];

        yield 'Trait' => [
            MakerConfigTestHelper::trait(),
            'App\Trait',
            'DummyTrait',
            '/srv/app/src/Trait/DummyTrait.php',
            TraitForObjectBuilder::class,
        ];

        yield 'EntityTrait' => [
            MakerConfigTestHelper::entityTrait(),
            'App\Trait',
            'DummyEntityTrait',
            '/srv/app/src/Trait/DummyEntityTrait.php',
            TraitForEntityBuilder::class,
        ];

        yield 'VO' => [
            MakerConfigTestHelper::voForObject(),
            'App\VO',
            'Dummy',
            '/srv/app/src/VO/Dummy.php',
            VoForObjectBuilder::class,
        ];

        yield 'VO for entity' => [
            MakerConfigTestHelper::voForEntity(),
            'App\VO\Entity',
            'Dummy',
            '/srv/app/src/VO/Entity/Dummy.php',
            VoForEntityBuilder::class,
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param MakerConfig $config
     * @param string $expectedNamespace
     * @param string $expectedClassname
     * @param string $expectedAbsolutePath
     * @param string $expectedBuilder
     * @return void
     */
    public function testFileDefinition(
        MakerConfig $config,
        string $expectedNamespace,
        string $expectedClassname,
        string $expectedAbsolutePath,
        string $expectedBuilder
    )
    {
        $fileDefinition = FileDefinition::create($config);

        self::assertSame($expectedNamespace, $fileDefinition->namespace());
        self::assertSame($expectedClassname, $fileDefinition->classname());
        self::assertSame($expectedAbsolutePath, $fileDefinition->absolutePath());
        self::assertSame($expectedBuilder, $fileDefinition->builder());
    }
}
