<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAccessorsTrait;
use function Symfony\Component\String\u;

class EventMakerConfiguration extends MakerConfiguration
{
    use PropertiesAccessorsTrait;
    use PropertiesAllowedTypesTrait;

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Event')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }
}
