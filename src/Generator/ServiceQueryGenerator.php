<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\ServiceQueryBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class ServiceQueryGenerator extends AbstractServiceGenerator
{
    #[\Override] protected function serviceDefinition(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder
    {
        return ServiceQueryBuilder::build($config, $namespace, $name);
    }

    #[\Override] protected function attribute(MakerConfig $config): string
    {
        return $config->rootNamespace().'\\Attribute\\QueryService';
    }
}
