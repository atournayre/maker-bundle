<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Printer;

final class Printer extends \Nette\PhpGenerator\Printer
{
    public string $indentation = "    ";

    public int $linesBetweenMethods = 1;

    public int $linesBetweenUseTypes = 1;
}
