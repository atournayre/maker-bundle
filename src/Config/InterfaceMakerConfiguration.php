<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class InterfaceMakerConfiguration extends MakerConfiguration
{
    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Interface')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }
}