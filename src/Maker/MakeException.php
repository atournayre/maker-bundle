<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\VO\Builder\ExceptionBuilder;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AutoconfigureTag('maker.command')]
class MakeException extends AbstractMaker
{
    private string $exceptionType;
    private ?string $exceptionNamedConstructor = null;

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
        return 'make:new:exception';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new exception')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the interface <fg=yellow>(e.g. App\Exception\Dummy)</>');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new Exception');
        $namespace = $input->getArgument('namespace');

        $configurations = [
            (new MakerConfig(
                namespace: $namespace,
                classnameSuffix: '',
                generator: ExceptionBuilder::class,
            ))
                ->withExtraProperty('exceptionType', $this->exceptionType)
                ->withExtraProperty('exceptionNamedConstructor', $this->exceptionNamedConstructor),
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
        return 'Create a new Exception';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionExceptionType = new ChoiceQuestion('Choose the exception type', [
            \Exception::class,
            \RuntimeException::class,
            \InvalidArgumentException::class,
            \LogicException::class,
        ]);

        $exceptionType = $io->askQuestion($questionExceptionType);

        $this->exceptionType = $exceptionType;

        $questionNamedConstructor = new Question('Add a named constructor. Leave empty to skip.');
        $namedConstructor = (string)$io->askQuestion($questionNamedConstructor);

        if ('' === $namedConstructor) {
            return;
        }

        $this->exceptionNamedConstructor = $namedConstructor;
    }
}
