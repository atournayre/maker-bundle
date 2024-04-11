<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\VO\Builder\FromTemplateBuilder;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AutoconfigureTag('maker.command')]
class MakeProjectInstall extends AbstractMaker
{
    private bool $enableApiPlatform = false;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string        $rootDir,
        #[Autowire('%atournayre_maker.root_namespace%')]
        private readonly string        $rootNamespace,
        private readonly FileGenerator $fileGenerator,
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'project:getting-started';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addOption('enable-api-platform', null, InputOption::VALUE_OPTIONAL, 'Enable ApiPlatform', false);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating project files');

        $configurations = $this->configurations('');

        $this->fileGenerator->generate($configurations);

        $this->writeSuccessMessage($io);

        $fileDefinitionCollection = FileDefinitionCollection::fromConfigurations($configurations, $this->rootNamespace, $this->rootDir);

        $files = array_map(
            fn(FileDefinition $fileDefinition) => $fileDefinition->absolutePath(),
            $fileDefinitionCollection->getFileDefinitions()
        );
        foreach ($files as $file) {
            $io->text(sprintf('Created: %s', $file));
        }
    }

    public static function getCommandDescription(): string
    {
        return 'Create multiple files to get started with a new project';
    }

    private function configurations(string $namespace): array
    {
        $templates = $this->getTemplates();
        $configurations = [];
        foreach ($templates as $template) {
            $configurations[] = (new MakerConfig(
                enableApiPlatform: $this->enableApiPlatform,
                namespace: $namespace,
                generator: FromTemplateBuilder::class,
            ))->withTemplatePath($template);
        }
        return $configurations;
    }

    private function getTemplates(): array
    {
        return [
            'Attribute/CommandService.php',
            'Attribute/QueryService.php',
            'ArgumentValueResolver/ContextArgumentValueResolver.php',
            'Exception/FailFast.php',
            'Factory/ContextFactory.php',
            'Helper/AttributeHelper.php',
            'Contracts/Logger/LoggerInterface.php',
            'Contracts/Response/ResponseInterface.php',
            'Contracts/Routing/RoutingInterface.php',
            'Contracts/Security/SecurityInterface.php',
            'Contracts/Security/UserInterface.php',
            'Contracts/Service/CommandServiceInterface.php',
            'Contracts/Service/FailFastInterface.php',
            'Contracts/Service/PostConditionsChecksInterface.php',
            'Contracts/Service/PreConditionsChecksInterface.php',
            'Contracts/Service/QueryServiceInterface.php',
            'Contracts/Service/TagCommandServiceInterface.php',
            'Contracts/Service/TagQueryServiceInterface.php',
            'Contracts/Templating/TemplatingInterface.php',
            'Contracts/Type/Primitive/ScalarObjectInterface.php',
            'Logger/AbstractLogger.php',
            'Logger/DefaultLogger.php',
            'Logger/NullLogger.php',
            'Service/Response/SymfonyResponseService.php',
            'Service/Routing/SymfonyRoutingService.php',
            'Service/Security/SymfonySecurityService.php',
            'Service/Templating/TwigTemplatingService.php',
            'Service/CommandService.php',
            'Service/QueryService.php',
            'Trait/EntityIsTrait.php',
            'Trait/IdEntityTrait.php',
            'Trait/IsTrait.php',
            'Type/Primitive/AbstractCollectionType.php',
            'Type/Primitive/BooleanType.php',
            'Type/Primitive/IntegerType.php',
            'Type/Primitive/ListImmutableType.php',
            'Type/Primitive/ListType.php',
            'Type/Primitive/MapImmutableType.php',
            'Type/Primitive/MapType.php',
            'Type/Primitive/StringType.php',
            'VO/Context.php',
            'VO/DateTime.php',
            'VO/Null/NullUser.php',
        ];
    }
}
