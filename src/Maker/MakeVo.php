<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the VO <fg=yellow>(e.g. Dummy)</>');
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

        $defaultType = MakeHelper::fieldDefaultType($fieldName);

        $type = null;

        while (null === $type) {
            $question = new Question('Field type (enter <comment>?</comment> to see all types)', $defaultType);
            $question->setAutocompleterValues(MakeHelper::allowedTypes());
            $type = $io->askQuestion($question);

            if ('?' === $type) {
                $io->writeln(MakeHelper::allowedTypes());
                $io->writeln('');

                $type = null;
            } elseif (!\in_array($type, MakeHelper::allowedTypes())) {
                $io->writeln(MakeHelper::allowedTypes());
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
                namespacePrefix: $this->configNamespaces->vo
            ))->withVoEntityNamespace();
        } else {
            $configurations[] = new MakerConfig(
                namespace: $namespace,
                builder: VoForObjectBuilder::class,
                voProperties: $this->voProperties,
                namespacePrefix: $this->configNamespaces->vo,
            );
        }

        return $configurations ?? [];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        MakeHelper::configureDependencies($dependencies, [
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
        ]);
    }
}
