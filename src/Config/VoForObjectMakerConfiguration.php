<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAccessorsTrait;

class VoForObjectMakerConfiguration extends MakerConfiguration
{
    use PropertiesAccessorsTrait;
    use PropertiesAllowedTypesTrait;
}
