<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Printer;

use Nette\PhpGenerator\PhpFile;

final class PhpFilePrinter
{
    public static function print(PhpFile $phpFile): string
    {
        return (new Printer())
            ->printFile($phpFile);
    }
}
