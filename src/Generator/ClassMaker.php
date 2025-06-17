<?php

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

/**
 * Maker for generating a simple class.
 */
class ClassMaker extends AbstractMaker
{
    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src'
    ) {
        parent::__construct($twig, $namespacePrefix, $dirPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandName(): string
    {
        return 'make:atournayre:class';
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandDescription(): string
    {
        return 'Creates a new PHP class';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the class (e.g. <fg=yellow>User</>)')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the class', '')
            ->addOption('extends', null, InputOption::VALUE_OPTIONAL, 'The parent class to extend from')
            ->addOption('implements', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The interfaces to implement', [])
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'The class description')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        $className = $input->getArgument('name');
        $namespace = $this->getNamespace($input->getOption('namespace'));
        
        $extends = $input->getOption('extends');
        $implements = $input->getOption('implements');
        $description = $input->getOption('description');
        
        // Generate the class file
        $classPath = $this->getPath(str_replace('\\', '/', $input->getOption('namespace'))) . '/' . $className . '.php';
        
        $this->generateFile($classPath, 'class/Class.twig', [
            'namespace' => $namespace,
            'class_name' => $className,
            'extends' => $extends,
            'implements' => $implements,
            'description' => $description,
            'uses' => [],
            'attributes' => [],
            'properties' => [],
            'constructor' => null,
            'methods' => [],
        ]);
        
        $io->text([
            'Class successfully generated!',
            'Path: ' . $classPath,
        ]);
    }
}
