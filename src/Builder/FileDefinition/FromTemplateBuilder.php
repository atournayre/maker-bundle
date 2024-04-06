<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class FromTemplateBuilder
{
    public static function build(
        MakerConfig $config,
        string $template,
        string $nameSuffix
    ): FileDefinitionBuilder
    {
        return FileDefinitionBuilder::buildFromTemplate($template, $nameSuffix, $config);
    }
}
