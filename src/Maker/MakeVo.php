<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\VoForEntityMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\VoForObjectMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Traits\PropertiesTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeVo extends AbstractMaker
{
    use PropertiesTrait;
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

    /**
     * @param ConsoleStyle $io
     * @param array<string, PropertyDefinition> $fields
     * @param bool $isFirstField
     * @return ?PropertyDefinition
     */
    private function askForNextField(ConsoleStyle $io, array $fields, bool $isFirstField): ?PropertyDefinition
    {
        $io->newLine();

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

        $allowedTypes = $this->configResources->valueObject->allowedTypes($this->filesystem);

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

        return PropertyDefinition::fromArray(['fieldName' => $fieldName, 'type' => $type]);
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

            $currentFields[$newField->fieldName] = $newField;
        }

        $this->properties = $currentFields;
    }

    /**
     * @return array<string>
     */
    private function entities(): array
    {
        return $this->filesystem->findFilesInDirectory($this->bundleConfiguration->directories->entity);
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        if ($this->voRelatedEntity) {
            return MakerConfigurationCollection::createAsList([
                VoForEntityMakerConfiguration::fromNamespace(
                    rootDir: $this->rootDir,
                    rootNamespace: $this->rootNamespace,
                    namespace: $this->configNamespaces->voEntity,
                    className: $namespace,
                )
                    ->withRelatedEntity(Str::prefixByRootNamespace(Str::namespaceFromPath($this->voRelatedEntity, $this->rootDir), $this->rootNamespace))
                    ->withProperties($this->properties)
                    ->withPropertiesAllowedTypes($this->configResources->valueObject->allowedTypes($this->filesystem)
                )
            ]);
        }

        return MakerConfigurationCollection::createAsList([
            VoForObjectMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->vo,
                className: $namespace,
            )
                ->withProperties($this->properties)
                ->withPropertiesAllowedTypes($this->configResources->valueObject->allowedTypes($this->filesystem)
            )
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
        ];
    }
}
