<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Contracts\Event\HasEventsInterface;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\AddEventsToEntityBuilder;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
     * @return MakerConfig[]
     */
    protected function configurations(string $namespace): array
    {
        return array_map(
            fn(string $entity) => new MakerConfig(
                namespace: Str::prefixByRootNamespace(Str::namespaceFromPath($entity, $this->rootDir), $this->rootNamespace),
                builder: AddEventsToEntityBuilder::class,
            ),
            $this->entitiesWithoutEvents()
        );
    }

    private function entitiesWithoutEvents(): array
    {
        $entityDirectory = $this->configNamespaces->entity;

        $filesystem = new Filesystem();
        if (!$filesystem->exists($entityDirectory)) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($entityDirectory)
            ->name('*.php')
            ->notContains(HasEventsInterface::class)
        ;

        $entities = [];
        foreach ($finder as $file) {
            $entities[] = $file->getRealPath();
        }
        return $entities;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
