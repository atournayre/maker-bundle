<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\DTO\Config\BundleConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\Config\Namespaces;
use Atournayre\Bundle\MakerBundle\DTO\Config\Resources;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\Helper\UStr;
use Atournayre\Bundle\MakerBundle\Service\FilesystemService;
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
        protected readonly string              $rootDir,
        protected readonly FileGenerator       $fileGenerator,
        protected readonly BundleConfiguration $bundleConfiguration,
        protected readonly FilesystemService   $filesystem,
    )
    {
        $this->rootNamespace = $this->bundleConfiguration->rootNamespace;
        $this->configNamespaces = $this->bundleConfiguration->namespaces;
        $this->configResources = $this->bundleConfiguration->resources;
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = $this->dependencies();
        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }

    /**
     * @throws \Throwable
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $namespace = $input->hasArgument('namespace')
            ? UStr::cleanNamespace($input->getArgument('namespace'))->title()->toString()
            : '';

        $configurations = $this->configurations($namespace);

        $this->fileGenerator->generate($configurations);

        $this->writeSuccessMessage($io);

        foreach ($configurations->absolutePaths() as $file) {
            $io->text(sprintf('Created: %s', $file));
        }

        $this->updateConfig($io);
    }

    abstract protected function configurations(string $namespace): MakerConfigurationCollection;

    protected function updateConfig(ConsoleStyle $io): void
    {
        // no-op
    }
}
