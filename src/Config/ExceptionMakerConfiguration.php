<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class ExceptionMakerConfiguration extends MakerConfiguration
{
    private string $type = '';
    private ?string $namedConstructor = null;

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->trimSuffix('Exception')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function withType(string $type): self
    {
        $config = clone $this;
        $config->type = $type;
        return $config;
    }

    public function namedConstructor(): ?string
    {
        return $this->namedConstructor;
    }

    public function withNamedConstructor(string $namedConstructor): self
    {
        $config = clone $this;
        $config->namedConstructor = $namedConstructor;
        return $config;
    }

    public function hasNamedConstructor(): bool
    {
        return null !== $this->namedConstructor;
    }
}
