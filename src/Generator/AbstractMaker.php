<?php

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

/**
 * Base class for all makers.
 */
abstract class AbstractMaker implements MakerInterface
{
    protected Environment $twig;
    protected string $namespacePrefix;
    protected string $dirPrefix;

    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src'
    ) {
        $this->twig = $twig;
        $this->namespacePrefix = $namespacePrefix;
        $this->dirPrefix = $dirPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $this->doGenerate($input, $io);
            $io->success('Code generation completed successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred during code generation: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Perform the actual code generation.
     */
    abstract protected function doGenerate(InputInterface $input, SymfonyStyle $io): void;
    
    /**
     * Generate a file from a Twig template.
     */
    protected function generateFile(string $targetPath, string $template, array $parameters = []): void
    {
        $content = $this->twig->render($template, $parameters);
        
        $directory = dirname($targetPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        file_put_contents($targetPath, $content);
    }
    
    /**
     * Get the full namespace for a class.
     */
    protected function getNamespace(string $subNamespace = ''): string
    {
        return trim($this->namespacePrefix . '\\' . $subNamespace, '\\');
    }
    
    /**
     * Get the full path for a file.
     */
    protected function getPath(string $subPath = ''): string
    {
        return trim($this->dirPrefix . '/' . $subPath, '/');
    }
}
