<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\PhpFileBuilderInterface;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Symfony\Component\Filesystem\Filesystem;

final class FileGenerator
{
    public function __construct(
        /** @var iterable<PhpFileBuilderInterface> */
        private readonly iterable $builders
    )
    {
    }

    /**
     * @param MakerConfigurationCollection $configurations
     * @return void
     */
    public function generate(MakerConfigurationCollection $configurations): void
    {
        $makerConfigurationCollection = $this->createMakerConfigurationCollectionWithSourceCode($configurations);
        $this->generateFiles($makerConfigurationCollection);
    }

    private function createMakerConfigurationCollectionWithSourceCode(
        MakerConfigurationCollection $makerConfigurationCollection
    ): MakerConfigurationCollection
    {
        $newMakerConfigurationCollection = [];
        /** @var MakerConfiguration $configuration */
        foreach ($makerConfigurationCollection->values() as $configuration) {
            $configurationClass = get_class($configuration);

            /** @var PhpFileBuilderInterface $builder */
            foreach ($this->builders as $builder) {
                if (!$builder->supports($configurationClass)) {
                    continue;
                }

                $phpFileDefinition = $builder->createPhpFileDefinition($configuration);
                $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();
                $newMakerConfigurationCollection[$configuration->fqcn] = $configuration->withSourceCode($sourceCode);
            }
        }

        return MakerConfigurationCollection::createAsMap($newMakerConfigurationCollection);
    }

    private function generateFiles(MakerConfigurationCollection $makerConfigurationCollection): void
    {
        $makerConfigurations = $makerConfigurationCollection->values();

        /** @var MakerConfiguration $makerConfiguration */
        foreach ($makerConfigurations as $makerConfiguration) {
            $this->saveFile($makerConfiguration->absolutePath(), $makerConfiguration->sourceCode());
        }
    }

    protected function saveFile(string $filePath, string $content): void
    {
        (new Filesystem())->dumpFile($filePath, $content);
    }
}
