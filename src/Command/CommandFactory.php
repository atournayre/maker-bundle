<?php

namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;

/**
 * Factory for creating command instances for each maker service.
 */
class CommandFactory
{
    /**
     * @var iterable<MakerInterface>
     */
    private iterable $makers;
    
    /**
     * @param iterable<MakerInterface> $makers
     */
    public function __construct(iterable $makers)
    {
        $this->makers = $makers;
    }
    
    /**
     * Create a command for each maker service.
     *
     * @return array<MakerCommand>
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
     */
    public function createCommand(MakerInterface $maker): MakerCommand
    {
        return new MakerCommand($maker);
    }
}
