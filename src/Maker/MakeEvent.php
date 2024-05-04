<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\EventBuilder;
use Atournayre\Bundle\MakerBundle\VO\Builder\ListenerBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeEvent extends AbstractMaker
{
    private array $eventProperties = [];

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

    private function askForNextField(ConsoleStyle $io, array $fields, bool $isFirstField): ?array
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

        $allowedTypes = $this->allowedTypes($this->configResources->event);

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

        return ['fieldName' => $fieldName, 'type' => $type];
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

        $this->eventProperties = $currentFields;
    }

    protected function configurations(string $namespace): array
    {
        $makerConfigEvent = (new MakerConfig(
            namespace: $namespace,
            builder: EventBuilder::class,
            classnameSuffix: 'Event',
            namespacePrefix: $this->configNamespaces->event,
        ))
            ->withExtraProperty('eventProperties', $this->eventProperties)
            ->withExtraProperty('allowedTypes', $this->allowedTypes($this->configResources->event));

        $listenerNamespace = Str::replace($namespace, 'Event', 'Listener');
        $listenerNamespace = Str::replace($listenerNamespace, '\Listener\\', '\EventListener\\');

        $makerConfigListener = (new MakerConfig(
            namespace: $listenerNamespace,
            builder: ListenerBuilder::class,
            classnameSuffix: 'Listener',
            namespacePrefix: $this->configNamespaces->eventListener,
        ))
            ->withExtraProperty('eventNamespace', $makerConfigEvent->namespace())
            ->withExtraProperty('allowedTypes', $this->allowedTypes($this->configResources->event));

        return [
            $makerConfigEvent,
            $makerConfigListener,
        ];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
            \Symfony\Contracts\EventDispatcher\Event::class => 'symfony/event-dispatcher',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
