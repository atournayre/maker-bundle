<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class CollectionMakerConfiguration extends MakerConfiguration
{
    private bool $ofDecimals = false;
    private bool $isImmutable = false;
    private ?string $relatedObject = null;

    protected static function classNameSuffix(): string
    {
        return 'Collection';
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
