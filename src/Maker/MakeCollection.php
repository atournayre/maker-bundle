<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\CollectionBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeCollection extends AbstractMaker
{
    private ?string $collectionRelatedObject = null;
    private bool $collectionIsImmutable = true;

    public static function getCommandName(): string
    {
        return 'make:new:collection';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Collection')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Collection <fg=yellow>(e.g. App\\\\Collection\\\\DummyCollection)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Collection';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionImmutablility = new Question('Collection must be immutable? (yes/no)', 'yes');
        $this->collectionIsImmutable = $io->askQuestion($questionImmutablility) === 'yes';

        if (empty($this->relatedObjects())) {
            $io->error('No object found in the Entity, DTO or VO directories');
            return;
        }

        $questionCollectionRelatedObject = new ChoiceQuestion('Choose the object related to this Collection', $this->relatedObjects());
        $this->collectionRelatedObject = $io->askQuestion($questionCollectionRelatedObject);
    }

    private function relatedObjects(): array
    {
        return array_map(
            fn(string $file) => Str::namespaceFromPath($file, $this->rootDir),
            MakeHelper::findFilesInDirectory([
                Str::sprintf('%s/Entity', $this->rootDir),
                Str::sprintf('%s/DTO', $this->rootDir),
                Str::sprintf('%s/VO/Entity', $this->rootDir),
            ])
        );
    }

    protected function configurations(string $namespace): array
    {
        $configurations[] = (new MakerConfig(
            namespace: $namespace,
            builder: CollectionBuilder::class,
        ))
            ->withExtraProperty('collectionRelatedObject', Str::prefixByRootNamespace($this->collectionRelatedObject, $this->rootNamespace))
            ->withExtraProperty('collectionIsImmutable', $this->collectionIsImmutable);

        return $configurations ?? [];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Atournayre\Collection\TypedCollection::class => 'atournayre/collection',
            \Atournayre\Collection\TypedCollectionImmutable::class => 'atournayre/collection',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
