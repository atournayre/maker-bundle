<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\VO\Builder\AddAttributeBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceQueryBuilder;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;

#[AutoconfigureTag('maker.command')]
class MakeService extends AbstractMaker
{
    private const COMMAND = 'Command';
    private const QUERY = 'Query';

    private string $vo;
    private string $commandOrQuery;

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
        return 'make:new:service';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Service')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Service <fg=yellow>(e.g. App\Service\Command\DummyCommandService, App\Service\Query\DummyQueryService)</>')
            ->addOption('command', null, InputOption::VALUE_OPTIONAL, 'Create a Command Service', 0)
            ->addOption('query', null, InputOption::VALUE_OPTIONAL, 'Create a Query Service', 1)
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    /**
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new Service');
        $namespace = $input->getArgument('namespace');

        $builder = match ($this->commandOrQuery) {
            self::COMMAND => ServiceCommandBuilder::class,
            self::QUERY => ServiceQueryBuilder::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

        $attributeName = match ($this->commandOrQuery) {
            self::COMMAND => CommandService::class,
            self::QUERY => QueryService::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

        $configurations = [
            (new MakerConfig(
                namespace: $namespace,
                classnameSuffix: '',
                generator: $builder,
            ))->withExtraProperty('vo', $this->vo),
            (new MakerConfig(
                extraProperties: [
                    'serviceNamespace' => $namespace,
                    'attributes' => [
                        new Attribute($attributeName, [
                            'serviceName' => new Literal(u($namespace)->afterLast('\\')->append('::class')->toString())
                        ])
                    ],
                ],
                namespace: $this->vo,
                classnameSuffix: '',
                generator: AddAttributeBuilder::class,
            ))
                ->withRoot($this->rootNamespace, $this->rootDir)
                ->withTemplatePathFromNamespace(),
        ];

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
        return 'Create a new Service';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $availableVOs = $this->availableVOs();

        if (empty($availableVOs)) {
            $io->error('No VO available. Please create a VO first. Use "make:new:vo" command.');
            return;
        }

        $questionCommandOrQuery = new ChoiceQuestion('Choose the type of service', [
            self::COMMAND,
            self::QUERY,
        ], $input->getOption('command') ?? $input->getOption('query'));
        $commandOrQuery = $io->askQuestion($questionCommandOrQuery);

        $this->commandOrQuery = $commandOrQuery;

        $vo = new ChoiceQuestion('Choose the VO', $availableVOs);
        $voName = $io->askQuestion($vo);

        $this->vo = $voName;
    }

    private function availableVOs(): array
    {
        $finder = (new Finder())
            ->files()
            ->in('src/VO')
            ->name('*.php')
            ->sortByName();

        $vos = [];
        foreach ($finder as $file) {
            $namespace = u($file->getPathname())
                ->replace('src/', '')
                ->replace('/', '\\')
                ->replace('.php', '')
                ->toString();
            $vos[] = $namespace;
        }
        return $vos;
    }
}
