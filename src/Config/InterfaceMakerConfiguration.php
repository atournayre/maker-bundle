<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class InterfaceMakerConfiguration extends MakerConfiguration
{
    protected static function classNameSuffix(): string
    {
        return 'Interface';
    }
}
