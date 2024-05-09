<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Doctrine\ORM\Mapping\Id;
use Webmozart\Assert\Assert;
use Doctrine\DBAL\Types\Types;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\TraitForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\TraitForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Traits\PropertiesTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeTrait extends AbstractMaker
{
    use PropertiesTrait;
    private bool $enableApiPlatform = false;

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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the trait <fg=yellow>(e.g. DummyTrait)</>')
            ->addOption('enable-api-platform', null, InputOption::VALUE_OPTIONAL, 'Enable ApiPlatform', false)
        ;
    }

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        if ($this->traitIsUsedByEntity) {
            return MakerConfigurationCollection::createAsList([
                TraitForEntityMakerConfiguration::fromNamespace(
                    rootDir: $this->rootDir,
                    rootNamespace: $this->rootNamespace,
                    namespace: $this->configNamespaces->traitEntity,
                    className: $namespace,
                )
                    ->withIsUsedByEntity()
                    ->withProperties($this->properties)
                    ->withEnableApiPlatform($this->enableApiPlatform)
                    ->withPropertiesAllowedTypes($this->configResources->trait->allowedTypes($this->filesystem)
                )
            ]);
        }

        return MakerConfigurationCollection::createAsList([
            TraitForObjectMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->trait,
                className: $namespace,
            )
                ->withProperties($this->properties)
                ->withEnableApiPlatform($this->enableApiPlatform)
                ->withPropertiesAllowedTypes($this->configResources->trait->allowedTypes($this->filesystem))
        ]);
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

            if (!$newField instanceof PropertyDefinition) {
                break;
            }

            $currentFields[$newField->fieldName] = $newField;
        }

        $this->enableApiPlatform = (bool)$input->getOption('enable-api-platform');
        $this->properties = $currentFields;
        $this->traitIsUsedByEntity = $isUsedByEntity;
    }

    /**
     * @param array<string, PropertyDefinition> $fields
     */
    private function askForNextField(ConsoleStyle $io, array $fields, bool $isFirstField): ?PropertyDefinition
    {
        $io->newLine();

        if ($isFirstField) {
            $questionText = 'New property name (press <return> to stop adding fields)';
        } else {
            $questionText = 'Add another property? Enter the property name (or press <return> to stop adding fields)';
        }

        $fieldName = $io->ask($questionText, null, static function ($name) use ($fields) {
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

        $allowedTypes = $this->configResources->trait->allowedTypes($this->filesystem);

        $type = null;

        while (null === $type) {
            $question = new ChoiceQuestion('Field type', $allowedTypes);
            $type = $io->askQuestion($question);

            if ('?' === $type) {
                $io->writeln($allowedTypes);
                $io->writeln('');

                $type = null;
            } elseif (!\in_array($type, $allowedTypes)) {
                $io->writeln($allowedTypes);
                $io->error(sprintf('Invalid type "%s".', $type));
                $io->writeln('');

                $type = null;
            }
        }

        // this is a normal field
        $data = ['fieldName' => $fieldName, 'type' => $type, 'nullable' => false];

        if ('datetime' === $type) {
            return PropertyDefinition::fromArray($data);
        }

        if ($io->confirm('Can this field be null (nullable)', false)) {
            $data['nullable'] = true;
        }

        return PropertyDefinition::fromArray($data);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [
            Id::class => 'orm',
            Assert::class => 'webmozart/assert',
            Types::class => 'doctrine/dbal',
        ];
    }
}
