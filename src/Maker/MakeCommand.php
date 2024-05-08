<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\CommandMakerConfiguration;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeCommand extends AbstractMaker
{
    private string $commandTitle = '';
    private string $commandDescription = '';
    private string $commandName = '';

    public static function getCommandName(): string
    {
        return 'make:new:command';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Command')
            ->addArgument('name', InputArgument::REQUIRED, sprintf('Choose a command name (e.g. <fg=yellow>app:%s</>)', Str::asCommand(Str::getRandomTerm())))
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Command <fg=yellow>(e.g. DummyCommand)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Command';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $title = new Question('Choose a title for your command?');
        try {
            $this->commandTitle = $io->askQuestion($title);
        } catch (\Throwable $e) {
            throw new \RuntimeException('A title is required.', 0, $e);
        }

        $description = new Question('Choose a description for your command. Press <Enter> to skip.');
        $this->commandDescription = $io->askQuestion($description) ?? '';

        $this->commandName = trim($input->getArgument('name'));
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        return MakerConfigurationCollection::createAsList([
            CommandMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->command,
                className: $namespace,
            )
                ->withTitle($this->commandTitle)
                ->withDescription($this->commandDescription)
                ->withCommandName($this->commandName)
            ,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [
            Command::class => 'console',
        ];
    }
}
