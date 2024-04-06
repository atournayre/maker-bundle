<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\FromTemplateBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

abstract class AbstractGenerator
{
    /**
     * @var FileDefinitionBuilder[]
     */
    private array $filesDefinitions = [];
    private array $generatedFiles = [];

    public function __construct(
        protected string $rootDir,
        protected string $rootNamespace,
    )
    {
    }

    abstract public function generate(string $namespace, string $name, MakerConfig $config): void;

    protected function generateFiles(): void
    {
        $filesDefinitions = $this->getFilesDefinitionsWithContent();

        foreach ($filesDefinitions as $fileDefinition) {
            $this->saveFile($fileDefinition->absolutePath(), $fileDefinition->getContent());
            $this->generatedFiles[] = $fileDefinition->absolutePath();
        }
    }

    private function getFilesDefinitionsWithContent(): array
    {
        return $this->filesDefinitions;
    }

    protected function saveFile(string $filePath, string $file): void
    {
        (new Filesystem())->dumpFile($filePath, $file);
    }

    protected function addFileDefinition(FileDefinitionBuilder|array $fileDefinition): void
    {
        if (is_array($fileDefinition)) {
            Assert::allIsInstanceOf($fileDefinition, FileDefinitionBuilder::class);
            $this->filesDefinitions = array_merge($this->filesDefinitions, $fileDefinition);
            return;
        }

        $this->filesDefinitions[] = $fileDefinition;
    }

    protected function addFileDefinitionFromTemplate(string $type, string $template, MakerConfig $config): void
    {
        $this->addFileDefinition(FromTemplateBuilder::build($config, $template, $type));
    }

    public function getGeneratedFiles(): array
    {
        return $this->generatedFiles;
    }

    protected function addRootToConfig(MakerConfig $config): MakerConfig
    {
        return $config->withRoot($this->rootNamespace, $this->rootDir);
    }

    public function clearFilesDefinitions(): void
    {
        $this->filesDefinitions = [];
    }
}
