<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\CommandBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Command <fg=yellow>(e.g. App\\\\Command\\\\DummyCommand)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Command';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $title = new Question('Choose a title for your command?');
        $this->commandTitle = $io->askQuestion($title);

        $description = new Question('Choose a description for your command. Press <Enter> to skip.');
        $this->commandDescription = $io->askQuestion($description) ?? '';

        $this->commandName = trim($input->getArgument('name'));
    }

    protected function configurations(string $namespace): array
    {
        return [
            (new MakerConfig(
                namespace: $namespace,
                builder: CommandBuilder::class,
                classnameSuffix: 'Command',
            ))
                ->withExtraProperty('title', $this->commandTitle)
                ->withExtraProperty('description', $this->commandDescription)
                ->withExtraProperty('commandName', $this->commandName)
            ,
        ];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            Command::class => 'console',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
