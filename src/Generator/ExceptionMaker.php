<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

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
        if (empty($exceptionName)) {
            $exceptionName = $this->askForExceptionName($io);
        } elseif (str_ends_with((string) $exceptionName, 'Exception')) {
            $exceptionName = substr((string) $exceptionName, 0, -9); // Remove 'Exception' suffix
        }

        // 2. Get available exceptions (PHP core + project exceptions)
        $availableExceptions = $this->getAvailableExceptions();

        // 3. Ask for parent exception
        $parentException = $this->askForParentException($io, $availableExceptions);

        // 4. Determine namespace and directory
        $customNamespace = $input->getOption('namespace');
        $namespace = empty($customNamespace)
            ? $this->getExceptionNamespace()
            : $this->namespace($customNamespace);
        $directory = $this->getExceptionDirectory();

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
        $question = new Question('Choose exception name (without "Exception" suffix):');
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('The exception name cannot be empty.');
            }

            if (str_ends_with($answer, 'Exception')) {
                throw new \RuntimeException('Please provide the name without the "Exception" suffix.');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function getAvailableExceptions(): array
    {
        $exceptions = self::PHP_CORE_EXCEPTIONS;

        // Scan project for existing exceptions
        $projectExceptions = $this->findProjectExceptions();

        // Add project exceptions with a label to distinguish them
        foreach (array_keys($projectExceptions) as $exceptionClass) {
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
        $exceptionNamespace = $this->getExceptionNamespace();
        $exceptionDirectory = $this->getExceptionDirectory();

        // Only scan if the directory exists
        if (!is_dir($exceptionDirectory)) {
            return $exceptions;
        }

        $finder = new Finder();
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

    private function askForParentException(SymfonyStyle $io, array $availableExceptions): string
    {
        $question = new ChoiceQuestion(
            'Choose parent exception:',
            $availableExceptions,
            0 // Default to Exception
        );

        $choice = $io->askQuestion($question);

        // If it's a project exception, extract the class name
        if (str_contains((string) $choice, ' (from project)')) {
            return str_replace(' (from project)', '', $choice);
        }

        return $choice;
    }

    private function getExceptionNamespace(): string
    {
        return $this->namespace($this->exceptionNamespace ?? self::DEFAULT_EXCEPTION_NAMESPACE);
    }

    private function getExceptionDirectory(): string
    {
        return $this->path($this->exceptionDirectory ?? self::DEFAULT_EXCEPTION_DIRECTORY);
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
