<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\TraitForEntityBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\TraitForObjectBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function Symfony\Component\String\u;

#[AutoconfigureTag('maker.command')]
class MakeTrait extends AbstractMaker
{
    private bool $enableApiPlatform = false;
    private array $traitProperties = [];
    private bool $traitIsUsedByEntity = false;


    public static function getCommandName(): string
    {
        return 'make:new:trait';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Trait';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new trait')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the trait <fg=yellow>(e.g. App\Trait\DummyTrait)</>')
            ->addOption('enable-api-platform', null, InputOption::VALUE_OPTIONAL, 'Enable ApiPlatform', false)
        ;
    }

    protected function configurations(string $namespace): array
    {
        $namespace = u($namespace)->trimEnd('Trait');

        if ($this->traitIsUsedByEntity) {
            $suffix = 'EntityTrait';
            $configurations[] = new MakerConfig(
                namespace: $namespace->ensureEnd($suffix)->toString(),
                builder: TraitForEntityBuilder::class,
                enableApiPlatform: $this->enableApiPlatform,
                traitProperties: $this->traitProperties,
                traitIsUsedByEntity: true,
                classnameSuffix: $suffix,
            );
        } else {
            $suffix = 'Trait';
            $configurations[] = new MakerConfig(
                namespace: $namespace->ensureEnd($suffix)->toString(),
                builder: TraitForObjectBuilder::class,
                enableApiPlatform: $this->enableApiPlatform,
                traitProperties: $this->traitProperties,
                classnameSuffix: $suffix,
            );
        }

        return $configurations ?? [];
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $question = new Question('Do this trait will be used in an entity ? (yes/no)', 'no');
        $isUsedByEntity = $io->askQuestion($question) === 'yes';

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

        $this->enableApiPlatform = (bool)$input->getOption('enable-api-platform');
        $this->traitProperties = $currentFields;
        $this->traitIsUsedByEntity = $isUsedByEntity;
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

        if ($io->confirm('Can this field be null in the Trait (nullable)', false)) {
            $data['nullable'] = true;
        }

        return $data;
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

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Doctrine\ORM\Mapping::class => 'orm',
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
            \Doctrine\DBAL\Types\Types::class => 'doctrine/dbal',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
