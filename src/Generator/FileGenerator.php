<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

class FileGenerator
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string   $rootDir,
        #[Autowire('%atournayre_maker.root_namespace%')]
        private readonly string   $rootNamespace,
    )
    {
    }

    /**
     * @param array|MakerConfig[] $configurations
     * @return void
     */
    public function generate(array $configurations): void
    {
        Assert::allIsInstanceOf($configurations, MakerConfig::class);

        $fileDefinitionCollection = FileDefinitionCollection::fromConfigurations($configurations, $this->rootNamespace, $this->rootDir);

        $this->addSourceCodeToFilesDefinitions($fileDefinitionCollection);
        $this->generateFiles($fileDefinitionCollection);
    }

    private function addSourceCodeToFilesDefinitions(FileDefinitionCollection $fileDefinitionCollection): void
    {
        foreach ($fileDefinitionCollection->getFileDefinitions() as $fileDefinition) {
            $builder = $fileDefinition->builder().'::build';
            $builderCreate = $builder($fileDefinition);
            $sourceCode = $builderCreate->generate();
            $fileDefinitionCollection->set($fileDefinition->uniqueIdentifier(), $fileDefinition->withSourceCode($sourceCode));
        }
    }

    private function generateFiles(FileDefinitionCollection $fileDefinitionCollection): void
    {
        $filesDefinitions = $fileDefinitionCollection->getFileDefinitions();

        foreach ($filesDefinitions as $fileDefinition) {
            $this->saveFile($fileDefinition->absolutePath(), $fileDefinition->sourceCode());
        }
    }

    protected function saveFile(string $filePath, string $content): void
    {
        (new Filesystem())->dumpFile($filePath, $content);
    }
}
