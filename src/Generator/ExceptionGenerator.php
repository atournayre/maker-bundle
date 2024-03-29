<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Exception\ExceptionBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\AbstractGenerator;

class ExceptionGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition($this->exception($config, $namespace, $name));
        $this->generateFiles();
    }

    private function exception(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return ExceptionBuilder::build($config, $namespace, $name);
    }
}
