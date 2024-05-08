<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Webmozart\Assert\Assert;
use Symfony\Contracts\EventDispatcher\Event;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\EventMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\ListenerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Traits\PropertiesTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeEvent extends AbstractMaker
{
    use PropertiesTrait;

    public static function getCommandName(): string
    {
        return 'make:new:event';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Event (and Listener)')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Event <fg=yellow>(e.g. DummyEvent)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Event (and Listener)';
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

        $allowedTypes = $this->configResources->event->allowedTypes($this->filesystem);

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

        return PropertyDefinition::fromArray([
            'fieldName' => $fieldName,
            'type' => $type,
        ]);
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

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

        $this->properties = $currentFields;
    }

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $makerConfigEvent = EventMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $this->configNamespaces->event,
            className: $namespace,
        )
            ->withProperties($this->properties)
            ->withPropertiesAllowedTypes($this->configResources->event->allowedTypes($this->filesystem))
        ;

        $listenerNamespace = Str::replace($namespace, 'Event', 'Listener');
        $listenerNamespace = Str::replace($listenerNamespace, '\Listener\\', '\EventListener\\');

        $makerConfigListener = ListenerMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $this->configNamespaces->eventListener,
            className: $listenerNamespace,
        )
            ->withEventNamespace($makerConfigEvent->fqcn)
            ->withPropertiesAllowedTypes($this->configResources->event->allowedTypes($this->filesystem))
        ;

        return MakerConfigurationCollection::createAsList([
            $makerConfigEvent,
            $makerConfigListener,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [
            Assert::class => 'webmozart/assert',
            Event::class => 'symfony/event-dispatcher',
        ];
    }
}
