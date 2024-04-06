<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\LoggerInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\ResponseInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\RoutingInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\SecurityInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\TemplatingInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts\UserInterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Exception\FailFastExceptionBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\FromTemplateBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger\AbstractLoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger\LoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger\NullLoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\CommandAndQueryServicesBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\SymfonyResponseServiceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\SymfonyRoutingServiceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\SymfonySecurityServiceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\TwigTemplatingServiceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait\EntityIsTraitBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait\IdEntityTraitBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait\IsTraitBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class ProjectInstallGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition(LoggerInterfaceBuilder::build($config));
        $this->addFileDefinition(SecurityInterfaceBuilder::build($config));
        $this->addFileDefinition(UserInterfaceBuilder::build($config));
        $this->addFileDefinition(AbstractLoggerBuilder::build($config));
        $this->addFileDefinition(LoggerBuilder::build($config));
        $this->addFileDefinition(NullLoggerBuilder::build($config));
        $this->addFileDefinition(EntityIsTraitBuilder::build($config));
        $this->addFileDefinition(IsTraitBuilder::build($config));
        $this->addFileDefinition(IdEntityTraitBuilder::build($config));
        $this->addFileDefinition(ResponseInterfaceBuilder::build($config));
        $this->addFileDefinition(RoutingInterfaceBuilder::build($config));
        $this->addFileDefinition(TemplatingInterfaceBuilder::build($config));
        $this->addFileDefinition(TwigTemplatingServiceBuilder::build($config));
        $this->addFileDefinition(SymfonyResponseServiceBuilder::build($config));
        $this->addFileDefinition(SymfonyRoutingServiceBuilder::build($config));
        $this->addFileDefinition(SymfonySecurityServiceBuilder::build($config));
        $this->addFileDefinition(FailFastExceptionBuilder::build($config));
        $this->addFileDefinition(CommandAndQueryServicesBuilder::filesDefinitions($config));

        foreach ($this->templates() as $type => $templates) {
            foreach ($templates as $template) {
                $this->addFileDefinition(FromTemplateBuilder::build($config, $template, $type));
            }
        }

        $this->generateFiles();
    }

    private function templates(): array
    {
        return [
            'ArgumentValueResolver' => [
                'ArgumentValueResolver/ContextArgumentValueResolver.php',
            ],
            'Factory' => [
                'Factory/ContextFactory.php',
            ],
            'Interface' => [
                'Contracts/Type/Primitive/ScalarObjectInterface.php',
            ],
            'Type' => [
                'Type/Primitive/AbstractCollectionType.php',
                'Type/Primitive/BooleanType.php',
                'Type/Primitive/IntegerType.php',
                'Type/Primitive/ListImmutableType.php',
                'Type/Primitive/ListType.php',
                'Type/Primitive/MapImmutableType.php',
                'Type/Primitive/MapType.php',
                'Type/Primitive/StringType.php',
            ],
            '' => [
                'VO/Null/NullUser.php',
                'VO/Context.php',
                'VO/DateTime.php',
            ],
        ];
    }
}
