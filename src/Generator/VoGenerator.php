<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VO\VOBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class VoGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition($this->vo($config, $namespace, $name));
        $this->generateFiles();
    }

    private function vo(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return VOBuilder::build($config, $namespace, $name);
    }
}
