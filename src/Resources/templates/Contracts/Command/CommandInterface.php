<?php
declare(strict_types=1);

namespace App\Contracts\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface CommandInterface
{
    public function doExecute(InputInterface $input, SymfonyStyle $io): void;
}
