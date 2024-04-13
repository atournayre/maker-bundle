<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;

class AddAttributeBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $self = FromTemplateBuilder::build($fileDefinition);
        $class = $self->getClass();

        $config = $fileDefinition->configuration();
        $serviceNamespace = $config->getExtraProperty('serviceNamespace');
        $attributes = $config->getExtraProperty('attributes');

        $namespace = $class->getNamespace();
        $namespace->addUse($serviceNamespace);

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attributeName = '\\'.$attribute->getName();
            $namespace->addUse($attributeName);
        }

        $class->setAttributes($attributes);

        return $self;
    }
}
