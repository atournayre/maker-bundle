<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:dummy:command', description: 'Run me to see what happens')]
final class DummyCommand extends AbstractCommand
{
    public function doExecute(InputInterface $input, SymfonyStyle $io): void
    {
        // Add your logic here
    }

    protected function failFast(InputInterface $input, SymfonyStyle $io): void
    {
        // Implement method or remove it if not needed
    }

    protected function postConditionsChecks(InputInterface $input, SymfonyStyle $io): void
    {
        // Implement method or remove it if not needed
    }

    protected function preConditionsChecks(InputInterface $input, SymfonyStyle $io): void
    {
        // Implement method or remove it if not needed
    }

    public function title(): string
    {
        return 'I am a dummy command';
    }
}
