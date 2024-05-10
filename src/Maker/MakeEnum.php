<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use ArchTech\Enums\Comparable;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\EnumMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\CaseDefinition;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeEnum extends AbstractMaker
{
    /**
     * @var array|CaseDefinition[] $cases
     */
    private array $cases = [];

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        return MakerConfigurationCollection::createAsList([
            EnumMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->enum,
                className: $namespace,
            )
            ->withCases($this->cases)
        ]);
    }

    public static function getCommandName(): string
    {
        return 'make:new:enum';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Enum')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Enum <fg=yellow>(e.g. Dummy)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Enum';
    }

    /**
     * @param array<string, CaseDefinition> $fields
     */
    private function askForNextField(ConsoleStyle $io, array $fields, bool $isFirstField): ?CaseDefinition
    {
        $io->newLine();

        if ($isFirstField) {
            $questionText = 'New case name (press <return> to stop adding fields)';
        } else {
            $questionText = 'Add another case? Enter the case name (or press <return> to stop adding fields)';
        }

        $name = $io->ask($questionText, null, static function ($name) use ($fields) {
            // allow it to be empty
            if (!$name) {
                return $name;
            }

            if (\in_array($name, $fields)) {
                throw new \InvalidArgumentException(sprintf('The "%s" case already exists.', $name));
            }

            return $name;
        });

        if (!$name) {
            return null;
        }

        $question = new Question('Value (leave empty if you need pure enum)');
        $type = $io->askQuestion($question);

        return CaseDefinition::fromArray(['name' => $name, 'value' => $type]);
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $currentFields = [];

        $isFirstField = true;

        while (true) {
            $newField = $this->askForNextField($io, $currentFields, $isFirstField);

            $isFirstField = false;

            if (!$newField instanceof CaseDefinition) {
                break;
            }

            $currentFields[$newField->name] = $newField;
        }

        $this->cases = $currentFields;
        dump($currentFields);
    }

    protected function dependencies(): array
    {
        return [
            Comparable::class => 'archtech/enums',
        ];
    }
}
