<?php

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
    name: 'make:atournayre:placeholder',
    description: 'This is a placeholder command that will be replaced by concrete maker commands',
    hidden: true
)]
class MakerCommand extends Command
{
    private MakerInterface $maker;
    
    public function __construct(MakerInterface $maker)
    {
        $this->maker = $maker;
        
        parent::__construct($maker->getCommandName());
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription($this->maker->getCommandDescription());
            
        $this->maker->configureCommand($this);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->maker->generate($input, $output);
    }
}
