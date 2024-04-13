<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;

class FromTemplateBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        return (new self($fileDefinition))
            ->withFile($fileDefinition->toPhpFile());
    }
}
