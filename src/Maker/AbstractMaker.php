<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AbstractMaker extends \Symfony\Bundle\MakerBundle\Maker\AbstractMaker
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/src')]
        protected readonly string        $rootDir,
        #[Autowire('%atournayre_maker.root_namespace%')]
        protected readonly string        $rootNamespace,
        protected readonly FileGenerator $fileGenerator,
    )
    {
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $namespace = $input->hasArgument('namespace') ? $input->getArgument('namespace') : '';
        $namespace = Str::cleanNamespace($namespace);

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
    }

    abstract protected function configurations(string $namespace): array;
}