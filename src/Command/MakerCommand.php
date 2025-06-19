<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for all maker commands.
 */
#[AsCommand(
    name: 'make:elegant:placeholder',
    description: 'This is a placeholder command that will be replaced by concrete maker commands',
    hidden: true
)]
final class MakerCommand extends Command
{
    public function __construct(private readonly MakerInterface $maker)
    {
        parent::__construct($this->maker->commandName());
    }

    protected function configure(): void
    {
        $this
            ->setDescription($this->maker->commandDescription())
        ;

        $this->maker->configureCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->maker->generate($input, $output);
    }
}
