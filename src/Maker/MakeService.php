<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\AddAttributeBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceCommandBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ServiceQueryBuilder;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Service <fg=yellow>(e.g. App\Service\Command\DummyCommandService, App\Service\Query\DummyQueryService)</>')
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

    protected function configurations(string $namespace): array
    {
        return [
            (new MakerConfig(
                namespace: $namespace,
                classnameSuffix: '',
                generator: $this->builder,
            ))->withExtraProperty('vo', $this->vo),
            (new MakerConfig(
                extraProperties: [
                    'serviceNamespace' => $namespace,
                    'attributes' => [
                        new Attribute($this->attributeName, [
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
    }
}
