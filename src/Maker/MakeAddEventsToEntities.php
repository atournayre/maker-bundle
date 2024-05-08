<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Contracts\Event\HasEventsInterface;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Collection\SplFileInfoCollection;
use Atournayre\Bundle\MakerBundle\Config\AddEventsToEntitiesMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[AutoconfigureTag('maker.command')]
class MakeAddEventsToEntities extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:add:events-to-entities';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Add events to entities');
    }

    public static function getCommandDescription(): string
    {
        return 'Add events to entities';
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $configurations = $this->entitiesWithoutEvents()
            ->toMap()
            ->map(function (SplFileInfo $entityFile) {
                $fqcn = Str::prefixByRootNamespace(Str::namespaceFromPath($entityFile->getRealPath(), $this->rootDir), $this->rootNamespace);

                return AddEventsToEntitiesMakerConfiguration::fromFqcn(
                    rootDir: $this->rootDir,
                    rootNamespace: $this->rootNamespace,
                    fqcn: $fqcn,
                )->withSourceCode($entityFile->getContents());
            })
            ->toArray()
        ;
        return MakerConfigurationCollection::createAsList($configurations);
    }

    /**
     * @return SplFileInfoCollection
     */
    private function entitiesWithoutEvents(): SplFileInfoCollection
    {
        $entityDirectory = $this->bundleConfiguration->directories->entity;

        $filesystem = new Filesystem();
        if (!$filesystem->exists($entityDirectory)) {
            return SplFileInfoCollection::createAsMap([]);
        }

        $finder = (new Finder())
            ->files()
            ->in($entityDirectory)
            ->name('*.php')
            ->notContains(HasEventsInterface::class)
        ;

        $entities = iterator_to_array($finder->getIterator());
        return SplFileInfoCollection::createAsMap($entities);
    }

    public function dependencies(): array
    {
        return [
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
        ];
    }
}
