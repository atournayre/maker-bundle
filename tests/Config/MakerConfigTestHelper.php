<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Config;

use App\Attribute\QueryService;
use App\Service\CommandService;
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
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;

class MakerConfigTestHelper
{
    public static function dto(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\DTO\Dummy',
            builder: DtoBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            dtoProperties: [
                'name' => 'id',
                'type' => 'string',
                'nullable' => false,
            ],
        );
    }

    public static function exception(): MakerConfig
    {
        $config = new MakerConfig(
            namespace: 'App\Exception\Dummy',
            builder: ExceptionBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
        );
        return $config
            ->withExtraProperty('exceptionType', \Exception::class)
            ->withExtraProperty('exceptionNamedConstructor', 'from');

    }

    public static function interface(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\Contracts\DummyInterface',
            builder: InterfaceBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            classnameSuffix: 'Interface',
        );
    }

    public static function logger(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\Logger\DummyLogger',
            builder: LoggerBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            classnameSuffix: 'Logger',
        );
    }

    public static function commandService(): MakerConfig
    {
        return (new MakerConfig(
            namespace: 'App\Service\Command\DummyCommandService',
            builder: ServiceCommandBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            classnameSuffix: 'CommandService',
        ))->withExtraProperty('vo', 'App\VO\Dummy');
    }

    public static function commandServiceVOAttribute(): MakerConfig
    {
        return (new MakerConfig(
                namespace: 'App\VO\Dummy',
                builder: AddAttributeBuilder::class,
                extraProperties: [
                    'serviceNamespace' => 'App\Service\Command\DummyCommandService',
                    'attributes' => [
                        new Attribute(CommandService::class, [
                            'serviceName' => new Literal('DummyCommandService::class')
                        ])
                    ],
                ],
            ))
                ->withRoot('App', '/srv/app/src')
                ->withTemplatePathFromNamespace();
    }

    public static function queryService(): MakerConfig
    {
        return (new MakerConfig(
            namespace: 'App\Service\Query\DummyQueryService',
            builder: ServiceQueryBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            classnameSuffix: 'QueryService',
        ))->withExtraProperty('vo', 'App\VO\Dummy');
    }

    public static function queryServiceVOAttribute(): MakerConfig
    {
        return (new MakerConfig(
            namespace: 'App\VO\Dummy',
            builder: AddAttributeBuilder::class,
            extraProperties: [
                'serviceNamespace' => 'App\Service\Query\DummyQueryService',
                'attributes' => [
                    new Attribute(QueryService::class, [
                        'serviceName' => new Literal('DummyQueryService::class')
                    ])
                ],
            ],
        ))
            ->withRoot('App', '/srv/app/src')
            ->withTemplatePathFromNamespace();
    }

    public static function entityTrait(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\Trait\DummyEntityTrait',
            builder: TraitForEntityBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            enableApiPlatform: true,
            traitProperties: [
                'name' => 'id',
                'type' => 'string',
                'nullable' => false,
            ],
            traitIsUsedByEntity: true,
            classnameSuffix: 'EntityTrait',
        );
    }

    public static function trait(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\Trait\DummyTrait',
            builder: TraitForObjectBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            enableApiPlatform: true,
            traitProperties: [
                'name' => 'id',
                'type' => 'string',
                'nullable' => false,
            ],
            traitIsUsedByEntity: true,
            classnameSuffix: 'Trait',
        );
    }

    public static function voForEntity(): MakerConfig
    {
        return (new MakerConfig(
            namespace: 'App\VO\Entity\Dummy',
            builder: VoForEntityBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            voProperties: [
                'name' => 'id',
                'type' => 'string',
                'nullable' => false,
            ],
            voRelatedToAnEntity: 'App\Entity\Dummy',
        ))->withVoEntityNamespace();
    }

    public static function voForObject(): MakerConfig
    {
        return new MakerConfig(
            namespace: 'App\VO\Dummy',
            builder: VoForObjectBuilder::class,
            rootNamespace: 'App',
            rootDir: '/srv/app/src',
            voProperties: [
                'name' => 'id',
                'type' => 'string',
                'nullable' => false,
            ],
        );
    }
}
