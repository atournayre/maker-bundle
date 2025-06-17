<?php

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface for all maker services.
 */
interface MakerInterface
{
    /**
     * Return the command name (e.g. make:elegant:entity).
     */
    public function getCommandName(): string;

    /**
     * Return the command description.
     */
    public function getCommandDescription(): string;

    /**
     * Configure the command (add arguments, options, etc.).
     */
    public function configureCommand(Command $command): void;

    /**
     * Generate the code.
     * 
     * @return int The command exit code
     */
    public function generate(InputInterface $input, OutputInterface $output): int;
}
