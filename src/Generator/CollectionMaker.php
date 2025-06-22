<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Primitives\Collection;
use Atournayre\Primitives\StringType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

/**
 * Maker for generating collection classes.
 */
final class CollectionMaker extends AbstractMaker implements MakerInterface
{
    /**
     * @param array<int, string>|null $allowedNamespaces
     */
    public function __construct(
        Environment $twig,
        string $namespacePrefix,
        string $dirPrefix,
        private readonly ?string $collectionNamespace = null,
        private readonly ?string $collectionDirectory = null,
        private readonly ?array $allowedNamespaces = null,
    ) {
        parent::__construct($twig, $namespacePrefix, $dirPrefix);
    }

    public function commandName(): string
    {
        return 'make:elegant:collection';
    }

    public function commandDescription(): string
    {
        return 'Creates a new collection class';
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the collection class (without the "Collection" suffix)')
            ->addArgument('class', InputArgument::OPTIONAL, 'The name of the class that constitutes the elements of the collection')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace for the collection class')
            ->addOption('list', null, InputOption::VALUE_NONE, 'Implement AsListInterface')
            ->addOption('map', null, InputOption::VALUE_NONE, 'Implement AsMapInterface')
        ;
    }

    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        // Get collection name from input or ask for it
        $collectionName = $input->getArgument('name');
        if (null === $collectionName || '' === $collectionName) {
            $collectionName = $this->askForCollectionName($io);
        }

        // Ensure collection name ends with "Collection"
        if (StringType::of($collectionName)->endsWith('Collection')->asBool()) {
            $collectionName .= 'Collection';
        }

        // Get class name from input or ask for it
        $className = $input->getArgument('class');
        if (null === $className || '' === $className) {
            $className = $this->askForClassName($io);
        }

        // Get namespace from input or resolve it
        $namespace = $input->getOption('namespace');
        if (null === $namespace || '' === $namespace) {
            $namespace = $this->resolveCollectionNamespace();
        }

        // Get directory from namespace or resolve it
        $directory = $this->resolveCollectionDirectory();

        // Check if collection already exists
        $collectionPath = $directory.'/'.$collectionName.'.php';
        if (file_exists($collectionPath)) {
            throw $this->createRuntimeException(sprintf('Collection "%s" already exists in "%s".', $collectionName, $directory));
        }

        // Get interfaces options
        $implementsList = $input->getOption('list');
        $implementsMap = $input->getOption('map');

        // Generate collection class
        $this->generateCollectionClass(
            $collectionName,
            $className,
            $namespace,
            $directory,
            $implementsList,
            $implementsMap
        );

        $io->success(sprintf('Collection "%s" created in "%s".', $collectionName, $collectionPath));
    }

    private function askForCollectionName(SymfonyStyle $io): string
    {
        $question = $this->createQuestion('Collection name (without the "Collection" suffix):');
        $question->setValidator(function ($answer) {
            if (!$answer) {
                throw $this->createRuntimeException('Collection name cannot be empty.');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function askForClassName(SymfonyStyle $io): string
    {
        $question = $this->createQuestion('Class name for collection elements:');
        $question->setValidator(function ($answer) {
            if (!$answer) {
                throw $this->createRuntimeException('Class name cannot be empty.');
            }

            return $answer;
        });

        // If allowed namespaces are provided, use them for autocomplete
        if (null !== $this->allowedNamespaces && Collection::of($this->allowedNamespaces)->isEmpty()->no()) {
            $question->setAutocompleterValues($this->listAllowedClasses());
        }

        return $io->askQuestion($question);
    }

    /**
     * @return array<int, string>
     */
    private function listAllowedClasses(): array
    {
        // This would need to be implemented to scan the allowed namespaces for classes
        // For now, return an empty array
        return [];
    }

    private function createQuestion(string $questionText): Question
    {
        return new Question($questionText);
    }

    private function createRuntimeException(string $message): \RuntimeException
    {
        return new \RuntimeException($message);
    }

    private function resolveCollectionNamespace(): string
    {
        return $this->collectionNamespace ?? $this->namespace('Collection');
    }

    private function resolveCollectionDirectory(): string
    {
        return $this->collectionDirectory ?? $this->path('Collection');
    }

    private function generateCollectionClass(
        string $collectionName,
        string $className,
        string $namespace,
        string $directory,
        bool $implementsList,
        bool $implementsMap,
    ): void {
        // Prepare interfaces
        $interfaces = [];
        if ($implementsList) {
            $interfaces[] = 'AsListInterface';
        }

        if ($implementsMap) {
            $interfaces[] = 'AsMapInterface';
        }

        // If no interfaces are specified, implement both by default
        if ([] === $interfaces) {
            $interfaces = ['AsListInterface', 'AsMapInterface'];
        }

        // Prepare methods
        $methods = [];
        if (Collection::of($interfaces)->in('AsListInterface', true)->asBool()) {
            $methods[] = 'asList';
        }

        if (Collection::of($interfaces)->in('AsMapInterface', true)->asBool()) {
            $methods[] = 'asMap';
        }

        // Generate the collection class
        $this->generateFile(
            $directory.'/'.$collectionName.'.php',
            'collection.tpl.php',
            [
                'namespace' => $namespace,
                'collection_name' => $collectionName,
                'class_name' => $className,
                'interfaces' => $interfaces,
                'methods' => $methods,
            ]
        );
    }
}
