<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\TraitType;

abstract class AbstractBuilder
{
    protected PhpFile $file;

    protected function __construct(
        protected readonly FileDefinition $fileDefinition,
    )
    {
    }

    abstract public static function build(FileDefinition $fileDefinition): self;

    protected function withFile(PhpFile $file): self
    {
        $instance = clone $this;
        $instance->file = $file;

        return $instance;
    }

    public function generate(): string
    {
        return (string)$this->file;
    }

    public function getClass(): ClassType|TraitType|InterfaceType|EnumType
    {
        return $this->file->getClasses()[$this->fileDefinition->fullName()];
    }
}
