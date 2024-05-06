<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\AddAttributeMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\ServiceCommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeService extends NewAbstractMaker
{
    private const COMMAND = 'Command';
    private const QUERY = 'Query';

    private string $vo;
    private string $serviceType;
    private string $attributeName;

    public static function getCommandName(): string
    {
        return 'make:new:service';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Service')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Service <fg=yellow>(e.g. DummyCommandService, DummyQueryService)</>')
            ->addOption('command', null, InputOption::VALUE_OPTIONAL, 'Create a Command Service', 0)
            ->addOption('query', null, InputOption::VALUE_OPTIONAL, 'Create a Query Service', 1)
        ;
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
        $this->serviceType = $io->askQuestion($questionCommandOrQuery);

        $vo = new ChoiceQuestion('Choose the VO', $availableVOs);
        $voName = $io->askQuestion($vo);

        $this->vo = $voName;

        $this->attributeName = match ($this->serviceType) {
            self::COMMAND => CommandService::class,
            self::QUERY => QueryService::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };
    }

    /**
     * @return array<string>
     */
    private function availableVOs(): array
    {
        return array_map(
            fn(string $file) => Str::namespaceFromPath($file, $this->rootDir),
            $this->bundleConfiguration->resources->service->allowedTypes($this->filesystem)
        );
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $vo = Str::prefixByRootNamespace($this->vo, $this->rootNamespace);

        $configurations = MakerConfigurationCollection::createAsList([
            AddAttributeMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->vo,
                className: $vo,
            )
                ->withServiceNamespace($namespace)
                ->withAttributes([
                    new Attribute($this->attributeName, [
                        'serviceName' => new Literal(Str::classNameSemiColonFromNamespace($namespace))
                    ])
                ]),
        ]);

        $serviceNamespace = $this->serviceType === self::COMMAND
            ? $this->configNamespaces->serviceCommand
            : $this->configNamespaces->serviceQuery;

        $configurations[] = ServiceCommandMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $serviceNamespace,
            className: $namespace,
        )
            ->withVo($vo);

        return $configurations;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        MakeHelper::configureDependencies($dependencies, [
            \Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class => 'symfony/dependency-injection',
        ]);
    }
}
