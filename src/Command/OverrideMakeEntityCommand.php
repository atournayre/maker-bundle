<?php

namespace Atournayre\Bundle\MakerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'override:make-entity',
    description: 'Replace/restore make:entity command template.',
)]
class OverrideMakeEntityCommand extends Command
{
    private const ENTITY_TPL_PATH = 'vendor/symfony/maker-bundle/src/Resources/skeleton/doctrine/Entity.tpl.php';
    private const ENTITY_TPL_BACKUP_PATH = self::ENTITY_TPL_PATH . '.backup';
    private const OVERRITEN_ENTITY_TPL_PATH = 'Resources/doctrine/Entity.tpl.php';

    protected function configure(): void
    {
        $this
            ->addOption('restore', null, InputOption::VALUE_NONE, 'Restore original template.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('restore')) {
            $this->restore();
            $io->success('make:entity Doctrine template have been restored.');
            return Command::SUCCESS;
        }

        $this->backupFile();
        $this->replaceFile();

        $io->success('make:entity Doctrine template have been replaced.');

        return Command::SUCCESS;
    }

    private function restore(): void
    {
        if (file_exists(self::ENTITY_TPL_BACKUP_PATH)) {
            copy(self::ENTITY_TPL_BACKUP_PATH, self::ENTITY_TPL_PATH);
            unlink(self::ENTITY_TPL_BACKUP_PATH);
        }
    }

    private function backupFile(): void
    {
        if (!file_exists(self::ENTITY_TPL_BACKUP_PATH)) {
            copy(self::ENTITY_TPL_PATH, self::ENTITY_TPL_BACKUP_PATH);
        }
    }

    private function replaceFile(): void
    {
        if (file_exists(self::OVERRITEN_ENTITY_TPL_PATH)) {
            copy(self::OVERRITEN_ENTITY_TPL_PATH, self::ENTITY_TPL_PATH);
        }
    }
}
