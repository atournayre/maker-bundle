<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;

class AddAttributeBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();
        $file = $fileDefinition->toPhpFile();

        $self = (new self($fileDefinition))
            ->withFile($file);
        $class = $self->getClass();

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
