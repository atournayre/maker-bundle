<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Primitives\StringType;
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
    public function __construct(protected Environment $twig, protected string $namespacePrefix = 'App', protected string $dirPrefix = 'src')
    {
    }

    public function generate(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->doGenerate($input, $io);
            $io->success('Code generation completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error('An error occurred during code generation: '.$exception->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Perform the actual code generation.
     */
    abstract protected function doGenerate(InputInterface $input, SymfonyStyle $io): void;

    /**
     * Generate a file from a PHP template.
     *
     * @param array<string, mixed> $parameters
     */
    protected function generateFile(string $targetPath, string $template, array $parameters = []): void
    {
        // Extract parameters to make them available in the template
        extract($parameters); // @phpstan-ignore-line

        // Start output buffering to capture the template output
        ob_start();

        // Include the template file
        include __DIR__.'/../../templates/'.$template;

        // Get the content from the output buffer
        $content = ob_get_clean();

        // Create the directory if it doesn't exist
        $directory = dirname($targetPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Write the content to the target file
        file_put_contents($targetPath, $content);
    }

    /**
     * Return the full namespace for a class.
     */
    protected function namespace(string $subNamespace = ''): string
    {
        return StringType::of($this->namespacePrefix.'\\'.$subNamespace)->trim('\\')->toString();
    }

    /**
     * Return the full path for a file.
     */
    protected function path(string $subPath = ''): string
    {
        return StringType::of($this->dirPrefix.'/'.$subPath)->trim('/')->toString();
    }
}
