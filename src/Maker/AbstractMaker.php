<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\Config\Namespaces;
use Atournayre\Bundle\MakerBundle\DTO\Config\Resources;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractMaker extends \Symfony\Bundle\MakerBundle\Maker\AbstractMaker
{
    protected readonly string $rootNamespace;
    protected readonly Namespaces $configNamespaces;
    protected readonly Resources $configResources;

    public function __construct(
        protected readonly string        $rootDir,
        protected readonly FileGenerator $fileGenerator,
        protected readonly BundleConfiguration $bundleConfiguration,
    )
    {
        $this->rootNamespace = $this->bundleConfiguration->rootNamespace;
        $this->configNamespaces = $this->bundleConfiguration->namespaces;
        $this->configResources = $this->bundleConfiguration->resources;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $namespace = $input->hasArgument('namespace')
            ? Str::cleanNamespace($input->getArgument('namespace'))
            : '';

        $configurations = $this->configurations($namespace);

        $this->fileGenerator->generate($configurations);

        $this->writeSuccessMessage($io);

        $fileDefinitionCollection = FileDefinitionCollection::fromConfigurations($configurations, $this->rootNamespace, $this->rootDir);
        $files = array_map(
            fn(FileDefinition $fileDefinition) => $fileDefinition->absolutePath(),
            $fileDefinitionCollection->getFileDefinitions()
        );
        foreach ($files as $file) {
            $io->text(sprintf('Created: %s', $file));
        }

        $this->updateConfig($io);
    }

    abstract protected function configurations(string $namespace): array;

    protected function updateConfig(ConsoleStyle $io): void
    {
        // no-op
    }
}
