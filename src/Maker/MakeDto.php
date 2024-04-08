<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\FileDefinitionCollection;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\FileGenerator;
use Atournayre\Bundle\MakerBundle\VO\Builder\DtoBuilder;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AutoconfigureTag('maker.command')]
class MakeDto extends AbstractMaker
{
    private array $dtoProperties = [];

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string        $rootDir,
        #[Autowire('%atournayre_maker.root_namespace%')]
        private readonly string        $rootNamespace,
        private readonly FileGenerator $fileGenerator,
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:new:dto';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new DTO')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the DTO <fg=yellow>(e.g. App\DTO\Dummy)</>');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new DTO');
        $namespace = $input->getArgument('namespace');

        $configurations = [
            new MakerConfig(
                dtoProperties: $this->dtoProperties,
                namespace: $namespace,
                classnameSuffix: '',
                generator: DtoBuilder::class
            ),
        ];

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

    public static function getCommandDescription(): string
    {
        return 'Create a new DTO';
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

        // this is a normal field
        $data = ['fieldName' => $fieldName, 'type' => $type, 'nullable' => false];

        if ('datetime' === $type) {
            return $data;
        }

        if ($io->confirm('Can this field be null in the DTO (nullable)', false)) {
            $data['nullable'] = true;
        }

        return $data;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

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

        $this->dtoProperties = $currentFields;
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
        } elseif (0 === strpos($snakeCasedField, 'is_')) {
            $defaultType = 'boolean';
        } elseif (0 === strpos($snakeCasedField, 'has_')) {
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
}
