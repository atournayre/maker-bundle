<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Exception;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;

class FailFastExceptionBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Exception',
        string $name = 'FailFast'
    ): FileDefinitionBuilder
    {
        $config = $config
            ->withExtraProperty('exceptionType', \RuntimeException::class)
            ->withExtraProperty('exceptionNamedConstructor', 'ifTrue')
        ;

        return ExceptionBuilder::build($config, $namespace, $name);
    }
}
