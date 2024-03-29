<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use function Symfony\Component\String\u;

final class ClassNameBuilder
{
    private function __construct(
        private string $className,
    )
    {
    }

    public static function build(string $name): self
    {
        $name = u($name)
            ->replace('-', '')
            ->replace('_', '')
            ->replace('/', '')
            ->toString();

        return new self($name);
    }

    public function withSuffixe(string $suffixe): self
    {
        $self = clone $this;
        $self->className = u($this->className)
            ->trimSuffix($suffixe)
            ->append($suffixe)
            ->ensureEnd($suffixe)
            ->title()
            ->toString();

        return $self;
    }

    public function name(): string
    {
        return $this->className;
    }
}
