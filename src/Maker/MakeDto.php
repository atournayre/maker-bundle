<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\DtoMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Traits\PropertiesTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeDto extends AbstractMaker
{
    use PropertiesTrait;

    public static function getCommandName(): string
    {
        return 'make:new:dto';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new DTO')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the DTO <fg=yellow>(e.g. Dummy)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new DTO';
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

        $allowedTypes = $this->configResources->dto->allowedTypes($this->filesystem);

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

            $currentFields[$newField->fieldName] = $newField;
        }

        $this->properties = $currentFields;
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        return MakerConfigurationCollection::createAsList([
            DtoMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->dto,
                className: $namespace,
            )
                ->withProperties($this->properties)
                ->withPropertiesAllowedTypes($this->configResources->dto->allowedTypes($this->filesystem)),
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
