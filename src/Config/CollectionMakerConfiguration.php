<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class CollectionMakerConfiguration extends MakerConfiguration
{
    private bool $ofDecimals = false;
    private bool $isImmutable = false;
    private ?string $relatedObject = null;

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Collection')->toString();

        return parent::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function ofDecimals(): bool
    {
        return $this->ofDecimals;
    }

    public function withOfDecimals(bool $ofDecimals = true): self
    {
        $config = clone $this;
        $config->ofDecimals = $ofDecimals;
        return $config;
    }

    public function isImmutable(): bool
    {
        return $this->isImmutable;
    }

    public function withIsImmutable(bool $isImmutable): self
    {
        $config = clone $this;
        $config->isImmutable = $isImmutable;
        return $config;
    }

    public function relatedObject(): ?string
    {
        return $this->relatedObject;
    }

    public function withRelatedObject(string $relatedObject): self
    {
        $config = clone $this;
        $config->relatedObject = $relatedObject;
        return $config;
    }
}
