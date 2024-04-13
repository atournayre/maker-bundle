<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\VoForEntityBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\VoForObjectBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AutoconfigureTag('maker.command')]
class MakeVo extends AbstractMaker
{
    private array $voProperties = [];
    private ?string $voRelatedEntity = null;

    public static function getCommandName(): string
    {
        return 'make:new:vo';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new VO')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the VO <fg=yellow>(e.g. App\\\\VO\\\\Dummy)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new VO';
    }

    private function askForNextField(ConsoleStyle $io, array $fields, bool $isFirstField): ?array
    {
        $io->writeln('');

        if ($isFirstField) {
            $questionText = 'New property name (press <return> to stop adding fields)';
        } else {
            $questionText = 'Add another property? Enter the property name (or press <return> to stop adding fields)';
        }

        $fieldName = $io->ask($questionText, null, function ($name) use ($fields) {
            // allow it to be empty
            if (!$name) {
                return $name;
            }

            if (\in_array($name, $fields)) {
                throw new \InvalidArgumentException(sprintf('The "%s" property already exists.', $name));
            }

            return $name;
        });

        if (!$fieldName) {
            return null;
        }

        $defaultType = $this->fieldDefaultType($fieldName);

        $type = null;

        while (null === $type) {
            $question = new Question('Field type (enter <comment>?</comment> to see all types)', $defaultType);
            $question->setAutocompleterValues($this->allowedTypes());
            $type = $io->askQuestion($question);

            if ('?' === $type) {
                $io->writeln($this->allowedTypes());
                $io->writeln('');

                $type = null;
            } elseif (!\in_array($type, $this->allowedTypes())) {
                $io->writeln($this->allowedTypes());
                $io->error(sprintf('Invalid type "%s".', $type));
                $io->writeln('');

                $type = null;
            }
        }

        return ['fieldName' => $fieldName, 'type' => $type];
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionVoIsRelatedToEntity = new Question('Is this VO related to an entity? (yes/no)', 'no');
        $voIsRelatedToEntity = $io->askQuestion($questionVoIsRelatedToEntity);

        if ('yes' === $voIsRelatedToEntity) {
            if (empty($this->entities())) {
                $io->error('No entity found in the Entity directory');
                return;
            }
            $questionVoRelatedEntity = new ChoiceQuestion('Choose the entity related to this VO', $this->entities());
            $this->voRelatedEntity = $io->askQuestion($questionVoRelatedEntity);
        }

        $currentFields = [];

        $isFirstField = true;

        while (true) {
            $newField = $this->askForNextField($io, $currentFields, $isFirstField);

            $isFirstField = false;

            if (null === $newField) {
                break;
            }

            $currentFields[$newField['fieldName']] = $newField;
        }

        $this->voProperties = $currentFields;
    }

    private function fieldDefaultType(string $fieldName): string
    {
        $defaultType = 'string';
        // try to guess the type by the field name prefix/suffix
        // convert to snake case for simplicity
        $snakeCasedField = Str::asSnakeCase($fieldName);

        if ('_at' === $suffix = substr($snakeCasedField, -3)) {
            $defaultType = 'datetime';
        } elseif ('_id' === $suffix) {
            $defaultType = 'integer';
        } elseif (str_starts_with($snakeCasedField, 'is_')) {
            $defaultType = 'boolean';
        } elseif (str_starts_with($snakeCasedField, 'has_')) {
            $defaultType = 'boolean';
        }

        return $defaultType;
    }

    private function allowedTypes(): array
    {
        return [
            'string',
            'integer',
            'float',
            'boolean',
            'datetime',
        ];
    }

    private function entities(): array
    {
        $entityDirectory = Str::sprintf('%s/Entity', $this->rootDir);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($entityDirectory)) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($entityDirectory)
            ->name('*.php')
            ->sortByName();

        $entities = [];
        foreach ($finder as $file) {
            $entities[] = Str::namespaceFromPath($file->getPathname(), $this->rootDir);
        }
        return $entities;
    }

    protected function configurations(string $namespace): array
    {
        if ($this->voRelatedEntity) {
            $configurations[] = (new MakerConfig(
                namespace: $namespace,
                builder: VoForEntityBuilder::class,
                voProperties: $this->voProperties,
                voRelatedToAnEntity: $this->voRelatedEntity,
            ))->withVoEntityNamespace();
        } else {
            $configurations[] = new MakerConfig(
                namespace: $namespace,
                builder: VoForObjectBuilder::class,
                voProperties: $this->voProperties
            );
        }

        return $configurations ?? [];
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
