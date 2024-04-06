<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Type\Primitive;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;

class AbstractCollectionTypeBuilder implements FileDefinitionBuilderInterface
{
    private const TEMPLATE = 'Type/Primitive/AbstractCollectionType.php';

    public static function build(
        MakerConfig $config,
        string $namespace = '',
        string $name = ''
    ): FileDefinitionBuilder
    {
        return FileDefinitionBuilder::buildFromTemplate(self::TEMPLATE, 'Type', $config);
    }
}
