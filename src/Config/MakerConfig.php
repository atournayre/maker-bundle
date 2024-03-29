<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class MakerConfig
{
    public function __construct(
        private string $rootNamespace = 'App',
        private string $rootDir = '',
        private readonly bool $enableApiPlatform = false,
        private readonly bool $traitsCreateEntityId = false,
        private readonly array $dtoProperties = [],
        private readonly array $voProperties = [],
        private readonly ?string $voRelatedToAnEntity = null,
        private readonly array $traitProperties = [],
        private readonly bool $traitIsUsedByEntity = false,
        private readonly bool $traitSeparateAccessors = false,
        private array $extraProperties = [],
    )
    {
    }

    public static function default(): self
    {
        return new self();
    }

    public function rootNamespace(): string
    {
        return $this->rootNamespace;
    }

    public function rootDir(): string
    {
        return $this->rootDir;
    }

    public function withRoot(string $rootNamespace, string $rootDir): self
    {
        $config = clone $this;
        $config->rootNamespace = $rootNamespace;
        $config->rootDir = $rootDir;
        return $config;
    }

    public function isEnableApiPlatform(): bool
    {
        return $this->enableApiPlatform;
    }

    public function isTraitsCreateEntityId(): bool
    {
        return $this->traitsCreateEntityId;
    }

    public function dtoProperties(): array
    {
        return $this->dtoProperties;
    }

    public function voProperties(): array
    {
        return $this->voProperties;
    }

    public function voRelatedToAnEntity(): ?string
    {
        return $this->voRelatedToAnEntity;
    }

    public function traitProperties(): array
    {
        return $this->traitProperties;
    }

    public function traitIsUsedByEntity(): bool
    {
        return $this->traitIsUsedByEntity;
    }

    public function traitSeparateAccessors(): bool
    {
        return $this->traitSeparateAccessors;
    }

    public function extraProperties(): array
    {
        return $this->extraProperties;
    }

    public function withExtraProperty(string $name, mixed $value): self
    {
        $config = clone $this;
        $config->extraProperties[$name] = $value;
        return $config;
    }

    public function getExtraProperty(string $name): mixed
    {
        return $this->extraProperties[$name] ?? null;
    }
}