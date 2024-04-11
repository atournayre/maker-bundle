<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class MakerConfig
{
    public function __construct(
        private string           $rootNamespace = 'App',
        private string           $rootDir = '',
        private readonly bool    $enableApiPlatform = false,
        private readonly bool    $traitsCreateEntityId = false,
        private readonly array   $dtoProperties = [],
        private readonly array   $voProperties = [],
        private readonly ?string $voRelatedToAnEntity = null,
        private readonly array   $traitProperties = [],
        private readonly bool    $traitIsUsedByEntity = false,
        private readonly bool    $traitSeparateAccessors = false,
        private array            $extraProperties = [],
        private ?string          $namespace = null, // TODO remove nullable after refactoring
        private readonly ?string $classnameSuffix = null,
        private readonly ?string $generator = null, // TODO remove nullable after refactoring
        private ?string          $templatePath = null, // TODO remove nullable after refactoring
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

    public function hasExtraProperty(string $name): bool
    {
        return null !== $this->getExtraProperty($name);
    }

    public function namespace(): ?string // TODO remove nullable after refactoring
    {
        return $this->namespace;
    }

    public function classnameSuffix(): ?string
    {
        return $this->classnameSuffix;
    }

    public function generator(): ?string // TODO remove nullable after refactoring
    {
        return $this->generator;
    }

    public function templatePath(): ?string
    {
        return $this->templatePath;
    }

    public function withTemplatePathFromNamespace(): self
    {
        $templatePath = u($this->rootDir)
            ->append('/src/')
            ->append($this->namespace)
            ->append('.php')
            ->replace('\\', '/')
            ->toString();

        $config = clone $this;
        $config->templatePath = $templatePath;

        return $config;
    }

    public function withVoEntityNamespace(): self
    {
        $namespace = u($this->namespace)
            ->replace('\\VO\\', '\\VO\\Entity\\')
        ;

        $config = clone $this;
        $config->namespace = $namespace->toString();
        return $config;
    }
}
