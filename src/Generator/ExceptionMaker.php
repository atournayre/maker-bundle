<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Primitives\Collection;
use Atournayre\Primitives\StringType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Twig\Environment;

/**
 * Maker for generating exception classes.
 */
final class ExceptionMaker extends AbstractMaker implements MakerInterface
{
    private const DEFAULT_EXCEPTION_NAMESPACE = 'Exception';

    private const DEFAULT_EXCEPTION_DIRECTORY = 'Exception';

    private const PHP_CORE_EXCEPTIONS = [
        'Exception',
        'RuntimeException',
        'InvalidArgumentException',
        'LogicException',
        'DomainException',
        'UnexpectedValueException',
        'OutOfBoundsException',
        'OverflowException',
        'UnderflowException',
        'OutOfRangeException',
        'LengthException',
        'BadMethodCallException',
        'BadFunctionCallException',
    ];

    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src',
        private readonly ?string $exceptionNamespace = null,
        private readonly ?string $exceptionDirectory = null,
    ) {
        parent::__construct($twig, $namespacePrefix, $dirPrefix);
    }

    public function commandName(): string
    {
        return 'make:elegant:exception';
    }

    public function commandDescription(): string
    {
        return 'Creates a new exception class';
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the exception (without "Exception" suffix)')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the exception')
        ;
    }

    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        // 1. Get the exception name (from argument or ask interactively)
        $exceptionName = $input->getArgument('name');
        if (null === $exceptionName || '' === $exceptionName) {
            $exceptionName = $this->askForExceptionName($io);
        } elseif (StringType::of((string) $exceptionName)->endsWith('Exception')->asBool()) {
            $exceptionName = StringType::of((string) $exceptionName)->slice(0, -9)->toString(); // Remove 'Exception' suffix
        }

        // 2. Get available exceptions (PHP core + project exceptions)
        $availableExceptions = $this->collectAvailableExceptions();

        // 3. Ask for parent exception
        $parentException = $this->askForParentException($io, $availableExceptions);

        // 4. Determine namespace and directory
        $customNamespace = $input->getOption('namespace');
        $namespace = (null === $customNamespace || '' === $customNamespace)
            ? $this->resolveExceptionNamespace()
            : $this->namespace($customNamespace);
        $directory = $this->resolveExceptionDirectory();

        // 5. Confirm namespace and directory
        $io->section('Summary');
        $io->table(
            ['Property', 'Value'],
            [
                ['Exception name', $exceptionName.'Exception'],
                ['Parent exception', $parentException],
                ['Namespace', $namespace],
                ['Directory', $directory],
                ['File path', $directory.'/'.$exceptionName.'Exception.php'],
            ]
        );

        if (!$io->confirm('Do you want to generate this exception class?', true)) {
            $io->warning('Exception generation cancelled.');

            return;
        }

        // Generate the exception class
        $this->generateExceptionClass(
            $exceptionName,
            $parentException,
            $namespace,
            $directory
        );

        $io->success([
            'Exception class generated successfully!',
            'Path: '.$directory.'/'.$exceptionName.'Exception.php',
        ]);
    }

    private function askForExceptionName(SymfonyStyle $io): string
    {
        $question = $this->createQuestion('Choose exception name (without "Exception" suffix):');
        $question->setValidator(function ($answer) {
            if (null === $answer || '' === $answer) {
                throw $this->createRuntimeException('The exception name cannot be empty.');
            }

            if (StringType::of($answer)->endsWith('Exception')->asBool()) {
                throw $this->createRuntimeException('Please provide the name without the "Exception" suffix.');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function createQuestion(string $questionText): Question
    {
        return new Question($questionText);
    }

    private function createRuntimeException(string $message): \RuntimeException
    {
        return new \RuntimeException($message);
    }

    /**
     * @return array<int, string> List of available exception class names
     */
    private function collectAvailableExceptions(): array
    {
        $exceptions = self::PHP_CORE_EXCEPTIONS;

        // Scan project for existing exceptions
        $projectExceptions = $this->findProjectExceptions();

        // Add project exceptions with a label to distinguish them
        foreach (Collection::of($projectExceptions)->keys() as $exceptionClass) {
            $exceptions[] = $exceptionClass.' (from project)';
        }

        return $exceptions;
    }

    /**
     * Find all exception classes in the project.
     *
     * @return array<string, string> Array of exception class names mapped to their namespaces
     */
    private function findProjectExceptions(): array
    {
        $exceptions = [];
        $exceptionNamespace = $this->resolveExceptionNamespace();
        $exceptionDirectory = $this->resolveExceptionDirectory();

        // Only scan if the directory exists
        if (!is_dir($exceptionDirectory)) {
            return $exceptions;
        }

        $finder = $this->createFinder();
        $finder->files()
            ->in($exceptionDirectory)
            ->name('*Exception.php')
        ;

        foreach ($finder as $file) {
            $className = $file->getBasename('.php');
            $exceptions[$className] = $exceptionNamespace;
        }

        return $exceptions;
    }

    /**
     * @param array<int, string> $availableExceptions List of available exception class names
     */
    private function askForParentException(SymfonyStyle $io, array $availableExceptions): string
    {
        $question = $this->createChoiceQuestion(
            'Choose parent exception:',
            $availableExceptions,
            0 // Default to Exception
        );

        $choice = $io->askQuestion($question);

        // If it's a project exception, extract the class name
        if (StringType::of((string) $choice)->containsAny(' (from project)')->asBool()) {
            return StringType::of((string) $choice)->replace(' (from project)', '')->toString();
        }

        return $choice;
    }

    /**
     * @param array<int, string> $choices
     */
    private function createChoiceQuestion(string $questionText, array $choices, int $defaultChoice): ChoiceQuestion
    {
        return new ChoiceQuestion($questionText, $choices, $defaultChoice);
    }

    private function resolveExceptionNamespace(): string
    {
        return $this->namespace($this->exceptionNamespace ?? self::DEFAULT_EXCEPTION_NAMESPACE);
    }

    private function resolveExceptionDirectory(): string
    {
        return $this->path($this->exceptionDirectory ?? self::DEFAULT_EXCEPTION_DIRECTORY);
    }

    private function createFinder(): Finder
    {
        return new Finder();
    }

    private function generateExceptionClass(
        string $exceptionName,
        string $parentException,
        string $namespace,
        string $directory,
    ): void {
        $className = $exceptionName.'Exception';
        $filePath = $directory.'/'.$className.'.php';

        $this->generateFile($filePath, 'exception.tpl.php', [
            'namespace' => $namespace,
            'class_name' => $className,
            'parent_exception' => $parentException,
        ]);
    }
}
