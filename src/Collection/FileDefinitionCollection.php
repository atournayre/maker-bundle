<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;

class FileDefinitionCollection
{
    /**
     * @var FileDefinition[]
     */
    private array $fileDefinitions = [];

    private function __construct(FileDefinition ...$fileDefinitions)
    {
        $this->fileDefinitions = $fileDefinitions;
    }

    public function set(string $key, FileDefinition $fileDefinition): void
    {
        $this->fileDefinitions[$key] = $fileDefinition;
    }

    /**
     * @return FileDefinition[]
     */
    public function getFileDefinitions(): array
    {
        return $this->fileDefinitions;
    }

    /**
     * @param array|MakerConfig[] $configurations
     * @param string|null $rootNamespace
     * @param string|null $rootDir
     * @return self
     */
    public static function fromConfigurations(array $configurations, ?string $rootNamespace = null, ?string $rootDir = null): self
    {
        if (null !== $rootNamespace && null !== $rootDir) {
            $configurations = array_map(
                fn(MakerConfig $config) => $config->withRoot($rootNamespace, $rootDir),
                $configurations
            );
        }

        $fileDefinitions = [];
        foreach ($configurations as $configuration) {
            $fileDefinition = FileDefinition::create($configuration);
            $fileDefinitions[$fileDefinition->uniqueIdentifier()] = $fileDefinition;
        }

        return new self(...$fileDefinitions);
    }
}
