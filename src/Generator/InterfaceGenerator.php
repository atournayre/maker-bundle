<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\AbstractGenerator;

class InterfaceGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition($this->interface($config, $namespace, $name));
        $this->generateFiles();
    }

    private function interface(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return InterfaceBuilder::build($config, $namespace, $name);
    }
}
