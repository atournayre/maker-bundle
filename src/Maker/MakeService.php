<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\AddAttributeBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceQueryBuilder;
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
use Symfony\Component\Finder\Finder;

#[AutoconfigureTag('maker.command')]
class MakeService extends AbstractMaker
{
    private const COMMAND = 'Command';
    private const QUERY = 'Query';

    private string $vo;
    private string $builder;
    private string $attributeName;

    public static function getCommandName(): string
    {
        return 'make:new:service';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Service')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Service <fg=yellow>(e.g. App\\\\Service\\\\Command\\\\DummyCommandService, App\\\\Service\\\\Query\\\\DummyQueryService)</>')
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
        $commandOrQuery = $io->askQuestion($questionCommandOrQuery);

        $vo = new ChoiceQuestion('Choose the VO', $availableVOs);
        $voName = $io->askQuestion($vo);

        $this->vo = $voName;

        $this->builder = match ($commandOrQuery) {
            self::COMMAND => ServiceCommandBuilder::class,
            self::QUERY => ServiceQueryBuilder::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

        $this->attributeName = match ($commandOrQuery) {
            self::COMMAND => CommandService::class,
            self::QUERY => QueryService::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

    }

    private function availableVOs(): array
    {
        $finder = (new Finder())
            ->files()
            ->in(Str::sprintf('%s/VO', $this->rootDir))
            ->name('*.php')
            ->sortByName();

        $vos = [];
        foreach ($finder as $file) {
            $vos[] = Str::namespaceFromPath($file->getPathname(), $this->rootDir);
        }
        return $vos;
    }

    protected function configurations(string $namespace): array
    {
        $vo = Str::prefixByRootNamespace($this->vo, $this->rootNamespace);

        return [
            (new MakerConfig(
                namespace: $namespace,
                builder: $this->builder,
            ))->withExtraProperty('vo', $vo),
            (new MakerConfig(
                namespace: $vo,
                builder: AddAttributeBuilder::class,
                extraProperties: [
                    'serviceNamespace' => $namespace,
                    'attributes' => [
                        new Attribute($this->attributeName, [
                            'serviceName' => new Literal(Str::classNameSemiColonFromNamespace($namespace))
                        ])
                    ],
                ],
            ))
                ->withRoot($this->rootNamespace, $this->rootDir)
                ->withTemplatePathFromNamespace(),
        ];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class => 'symfony/dependency-injection',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
