<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts\Builder;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;

interface FileDefinitionBuilderInterface
{
    public static function build(MakerConfig $config, string $namespace = '', string $name = ''): FileDefinitionBuilder;

    public function generateSourceCode(FileDefinition $fileDefinition): string;
}
