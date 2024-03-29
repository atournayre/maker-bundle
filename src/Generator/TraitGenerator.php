<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\TraitBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class TraitGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        if ($config->traitSeparateAccessors()) {
            $this->generateSeparateTraits($config, $namespace, $name);
            return;
        }

        $this->generateSingleTrait($config, $namespace, $name);
    }

    private function generateSeparateTraits(MakerConfig $config, string $namespace, string $name): void
    {
        $this->addFileDefinition($this->traitPropertyOnly($config, $namespace, $name));
        $this->addFileDefinition($this->traitAccessorsOnly($config, $namespace, $name));
        $this->generateFiles();
    }

    private function traitPropertyOnly(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return TraitBuilder::buildPropertyOnly($config, $namespace, $name);
    }

    private function traitAccessorsOnly(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return TraitBuilder::buildAccessorsOnly($config, $namespace, $name);
    }

    private function generateSingleTrait(MakerConfig $config, string $namespace, string $name): void
    {
        $this->addFileDefinition($this->trait($config, $namespace, $name));
        $this->generateFiles();
    }

    private function trait(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return TraitBuilder::build($config, $namespace, $name);
    }
}
