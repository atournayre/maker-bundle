<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait\EntityIsTraitBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Trait\IdEntityTraitBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class EntityTraitGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition(EntityIsTraitBuilder::build($config));
//        $this->addFileDefinition(IdEntityTraitBuilder::build($config));
        $this->generateFiles();
    }
}
