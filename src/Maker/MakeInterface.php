<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\InterfaceGenerator;
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
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the interface');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Create new Interface');
        $path = 'Contracts';
        $name = $input->getArgument('name');

        $this->interfaceGenerator->generate($path, $name, MakerConfig::default());

        $this->writeSuccessMessage($io);

        foreach ($this->interfaceGenerator->getGeneratedFiles() as $file) {
            $io->text(sprintf('Created: %s', $file));
        }
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Interface';
    }
}
