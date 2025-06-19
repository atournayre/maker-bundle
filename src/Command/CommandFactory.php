<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;

/**
 * Factory for creating command instances for each maker service.
 */
final readonly class CommandFactory
{
    /**
     * @param iterable<MakerInterface> $makers
     */
    public function __construct(private iterable $makers)
    {
    }

    /**
     * Create a command for each maker service.
     *
     * @return array<MakerCommand>
     *
     * @api
     */
    public function createCommands(): array
    {
        $commands = [];

        foreach ($this->makers as $maker) {
            $commands[] = new MakerCommand($maker);
        }

        return $commands;
    }

    /**
     * Create a command for a specific maker service.
     * This is used by the service container to create command instances.
     *
     * @api
     */
    public function createCommand(MakerInterface $maker): MakerCommand
    {
        return new MakerCommand($maker);
    }
}
