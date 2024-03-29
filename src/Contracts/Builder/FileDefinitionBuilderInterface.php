<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts\Builder;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

interface FileDefinitionBuilderInterface
{
    public static function build(MakerConfig $config, string $namespace = '', string $name = ''): FileDefinitionBuilder;
}
