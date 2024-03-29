<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger\LoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Logger\NullLoggerBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class LoggerGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinition($this->logger($config, $namespace, $name));
        $this->generateFiles();
    }

    private function logger(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        if (in_array($name, ['Null', 'NullLogger', 'null', 'nullLogger'])) {
            return NullLoggerBuilder::build($config, $namespace, $name);
        }

        return LoggerBuilder::build($config, $namespace, $name);
    }
}
