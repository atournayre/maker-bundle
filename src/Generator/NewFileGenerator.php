<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Symfony\Component\Filesystem\Filesystem;

final class NewFileGenerator
{
    public function __construct(
        private readonly iterable $builders
    )
    {
    }

    /**
     * @param MakerConfigurationCollection $configurations
     * @return void
     * @throws \ReflectionException
     */
    public function generate(MakerConfigurationCollection $configurations): void
    {
        $makerConfigurationCollection = $this->createMakerConfigurationCollectionWithSourceCode($configurations);
        $this->generateFiles($makerConfigurationCollection);
    }

    /**
     * @throws \ReflectionException
     */
    private function createMakerConfigurationCollectionWithSourceCode(
        MakerConfigurationCollection $makerConfigurationCollection
    ): MakerConfigurationCollection
    {
        $newMakerConfigurationCollection = [];
        /** @var MakerConfiguration $configuration */
        foreach ($makerConfigurationCollection->values() as $configuration) {
            $configurationClass = get_class($configuration);

            foreach ($this->builders as $builder) {
                if (!$builder->supports($configurationClass)) {
                    continue;
                }

                $createInstanceAbstract = new \ReflectionMethod(AbstractBuilder::class, 'createInstance');
                $createInstanceBuilder = new \ReflectionMethod($builder, 'createInstance');

                if ($createInstanceAbstract->getDeclaringClass()->name === $createInstanceBuilder->getDeclaringClass()->name) {
                    $builder = $builder->createInstance($configuration);
                } else {
                    $builder = $builder->create($configuration);
                }

                $sourceCode = $builder->printPhpFile();
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
