<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;

class AddAttributeBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $config = $fileDefinition->configuration();
        $serviceNamespace = $config->getExtraProperty('serviceNamespace');
        $attributes = $config->getExtraProperty('attributes');

        return FromTemplateBuilder::build($fileDefinition)
            ->withUse($serviceNamespace)
            ->setAttributes($attributes);
    }
}
