<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Config;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
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
use PHPUnit\Framework\TestCase;

/**
 * @covers \Atournayre\Bundle\MakerBundle\Config\MakerConfig
 * @group Unit
 * @group Config
 */
class MakerConfigTest extends TestCase
{
    public function testDtoConfiguration(): void
    {
        $config = MakerConfigTestHelper::dto();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([
            'name' => 'id',
            'type' => 'string',
            'nullable' => false,
        ], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\DTO\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(DtoBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testExceptionConfiguration(): void
    {
        $config = MakerConfigTestHelper::exception();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([
            'exceptionType' => \Exception::class,
            'exceptionNamedConstructor' => 'from',
        ], $config->extraProperties());
        self::assertSame(\Exception::class, $config->getExtraProperty('exceptionType'));
        self::assertSame('from', $config->getExtraProperty('exceptionNamedConstructor'));
        self::assertTrue($config->hasExtraProperty('exceptionType'));
        self::assertTrue($config->hasExtraProperty('exceptionNamedConstructor'));
        self::assertSame('App\Exception\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(ExceptionBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testInterfaceConfiguration(): void
    {
        $config = MakerConfigTestHelper::interface();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foot'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\Contracts\DummyInterface', $config->namespace());
        self::assertSame('Interface', $config->classnameSuffix());
        self::assertSame(InterfaceBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testLoggerConfiguration(): void
    {
        $config = MakerConfigTestHelper::logger();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\Logger\DummyLogger', $config->namespace());
        self::assertSame('Logger', $config->classnameSuffix());
        self::assertSame(LoggerBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testCommandServiceConfiguration(): void
    {
        $config = MakerConfigTestHelper::commandService();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([
            'vo' => 'App\VO\Dummy',
        ], $config->extraProperties());
        self::assertSame('App\VO\Dummy', $config->getExtraProperty('vo'));
        self::assertTrue($config->hasExtraProperty('vo'));
        self::assertSame('App\Service\Command\DummyCommandService', $config->namespace());
        self::assertSame('CommandService', $config->classnameSuffix());
        self::assertSame(ServiceCommandBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Config\MakerConfig::withRoot
     * @covers \Atournayre\Bundle\MakerBundle\Config\MakerConfig::withTemplatePathFromNamespace
     * @return void
     */
    public function testCommandServiceVOAttributeConfiguration(): void
    {
        $config = MakerConfigTestHelper::commandServiceVOAttribute();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertTrue($config->hasExtraProperty('serviceNamespace'));
        self::assertTrue($config->hasExtraProperty('attributes'));
        self::assertSame('App\VO\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(AddAttributeBuilder::class, $config->generator());
        self::assertSame('/srv/app/src/VO/Dummy.php', $config->templatePath());
        self::assertTrue($config->hasTemplatePath());
    }

    public function testQueryServiceConfiguration(): void
    {
        $config = MakerConfigTestHelper::queryService();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([
            'vo' => 'App\VO\Dummy',
        ], $config->extraProperties());
        self::assertSame('App\VO\Dummy', $config->getExtraProperty('vo'));
        self::assertTrue($config->hasExtraProperty('vo'));
        self::assertSame('App\Service\Query\DummyQueryService', $config->namespace());
        self::assertSame('QueryService', $config->classnameSuffix());
        self::assertSame(ServiceQueryBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Config\MakerConfig::withRoot
     * @covers \Atournayre\Bundle\MakerBundle\Config\MakerConfig::withTemplatePathFromNamespace
     * @return void
     */
    public function testQueryServiceVOAttributeConfiguration(): void
    {
        $config = MakerConfigTestHelper::queryServiceVOAttribute();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertTrue($config->hasExtraProperty('serviceNamespace'));
        self::assertTrue($config->hasExtraProperty('attributes'));
        self::assertSame('App\VO\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(AddAttributeBuilder::class, $config->generator());
        self::assertSame('/srv/app/src/VO/Dummy.php', $config->templatePath());
        self::assertTrue($config->hasTemplatePath());
    }

    public function testEntityTraitConfiguration(): void
    {
        $config = MakerConfigTestHelper::entityTrait();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertTrue($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([
            'name' => 'id',
            'type' => 'string',
            'nullable' => false,
        ], $config->traitProperties());
        self::assertTrue($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\Trait\DummyEntityTrait', $config->namespace());
        self::assertSame('EntityTrait', $config->classnameSuffix());
        self::assertSame(TraitForEntityBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testTraitConfiguration(): void
    {
        $config = MakerConfigTestHelper::trait();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertTrue($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([
            'name' => 'id',
            'type' => 'string',
            'nullable' => false,
        ], $config->traitProperties());
        self::assertTrue($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\Trait\DummyTrait', $config->namespace());
        self::assertSame('Trait', $config->classnameSuffix());
        self::assertSame(TraitForObjectBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testVoForEntityConfiguration(): void
    {
        $config = MakerConfigTestHelper::voForEntity();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([
            'name' => 'id',
            'type' => 'string',
            'nullable' => false,
        ], $config->voProperties());
        self::assertSame('App\Entity\Dummy', $config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\VO\Entity\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(VoForEntityBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }

    public function testVoForObjectConfiguration(): void
    {
        $config = MakerConfigTestHelper::voForObject();

        self::assertSame('App', $config->rootNamespace());
        self::assertSame('/srv/app/src', $config->rootDir());
        self::assertFalse($config->isEnableApiPlatform());
        self::assertFalse($config->isTraitsCreateEntityId());
        self::assertSame([], $config->dtoProperties());
        self::assertSame([
            'name' => 'id',
            'type' => 'string',
            'nullable' => false,
        ], $config->voProperties());
        self::assertNull($config->voRelatedToAnEntity());
        self::assertSame([], $config->traitProperties());
        self::assertFalse($config->traitIsUsedByEntity());
        self::assertFalse($config->traitSeparateAccessors());
        self::assertSame([], $config->extraProperties());
        self::assertNull($config->getExtraProperty('foo'));
        self::assertFalse($config->hasExtraProperty('foo'));
        self::assertSame('App\VO\Dummy', $config->namespace());
        self::assertNull($config->classnameSuffix());
        self::assertSame(VoForObjectBuilder::class, $config->generator());
        self::assertNull($config->templatePath());
        self::assertFalse($config->hasTemplatePath());
    }
}
