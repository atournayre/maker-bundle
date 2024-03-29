<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\ServiceCommandGenerator;
use Atournayre\Bundle\MakerBundle\Generator\ServiceQueryGenerator;
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
        private readonly ServiceCommandGenerator $serviceCommandGenerator,
        private readonly ServiceQueryGenerator $serviceQueryGenerator,
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
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the Service')
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
        $name = $input->getArgument('name');

        $config = new MakerConfig(
            extraProperties: [
                'vo' => $this->vo,
            ],
        );

        $generator = match ($this->commandOrQuery) {
            self::COMMAND => $this->serviceCommandGenerator,
            self::QUERY => $this->serviceQueryGenerator,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

        $path = match ($this->commandOrQuery) {
            self::COMMAND => 'Service\\Command',
            self::QUERY => 'Service\\Query',
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };

        $generator->generate($path, $name, $config);

        $this->writeSuccessMessage($io);

        foreach ($generator->getGeneratedFiles() as $file) {
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
