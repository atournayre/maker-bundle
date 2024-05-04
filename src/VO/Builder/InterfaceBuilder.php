<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;

class InterfaceBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        return static::create($fileDefinition)
            ->createFileAsInterface()
        ;
    }
}
