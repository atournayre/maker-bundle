<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Generator\InterfaceGenerator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeInterface extends AbstractMaker
{
    public function __construct(
        private readonly InterfaceGenerator $interfaceGenerator,
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:new:interface';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new interface')
            ->addArgument('path', InputArgument::REQUIRED, 'The namespace path of the new Interface (e.g. <fg=yellow>catalog</> will be located in <fg=yellow>src/Contracts/Catalog</>')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the interface');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new Interface');
        $path = $input->getArgument('path');
        $name = $input->getArgument('name');

        $this->interfaceGenerator->generate($path, $name);

        $this->writeSuccessMessage($io);
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new interface';
    }
}
