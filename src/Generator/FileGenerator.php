<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

class FileGenerator
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string   $rootDir,
        #[Autowire('%atournayre_maker.root_namespace%')]
        private readonly string   $rootNamespace,
        #[TaggedIterator('atournayre_maker.builder')]
        private readonly iterable $builders = [],
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
            $builder = $this->getBuilder($fileDefinition);
            $sourceCode = $builder->generateSourceCode($fileDefinition);
            $fileDefinitionCollection->set($fileDefinition->uniqueIdentifier(), $fileDefinition->withSourceCode($sourceCode));
        }
    }

    private function getBuilder(FileDefinition $fileDefinition)
    {
        Assert::keyExists($this->getBuilders(), $fileDefinition->builder(), 'No builder found for ' . $fileDefinition->builder());

        return $this->getBuilders()[$fileDefinition->builder()];
    }

    private function getBuilders(): array
    {
        $builders = [];

        if (is_array($this->builders)) {
            $builders = $this->builders;
        }

        if (is_iterable($this->builders)) {
            $builders = iterator_to_array($this->builders);
        }

        $buildersNames = array_map(fn($object) => get_class($object), $builders);

        return array_combine($buildersNames, $builders);
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
