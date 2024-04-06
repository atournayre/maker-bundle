<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\LoggerGenerator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeLogger extends AbstractMaker
{
    public function __construct(
        private readonly LoggerGenerator $loggerGenerator,
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:new:logger';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new logger')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the logger');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new Logger');
        $path = 'Logger';
        $name = $input->getArgument('name');

        $this->loggerGenerator->generate($path, $name, MakerConfig::default());

        $this->writeSuccessMessage($io);

        foreach ($this->loggerGenerator->getGeneratedFiles() as $file) {
            $io->text(sprintf('Created: %s', $file));
        }
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Logger';
    }
}
