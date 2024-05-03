<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Webmozart\Assert\Assert;

class MakerConfig
{
    public function __construct(
        private string           $namespace,
        private readonly string  $builder,
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
        private readonly ?string $classnameSuffix = null,
        private ?string          $templatePath = null,
        private readonly ?string $namespacePrefix = null,
    )
    {
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

    public function namespace(): string
    {
        if (null !== $this->namespacePrefix) {
            return sprintf('%s\%s', $this->namespacePrefix, $this->namespace);
        }

        return $this->namespace;
    }

    public function classnameSuffix(): ?string
    {
        return $this->classnameSuffix;
    }

    public function generator(): string
    {
        return $this->builder;
    }

    public function templatePath(): ?string
    {
        return $this->templatePath;
    }

    public function hasTemplatePath(): bool
    {
        return null !== $this->templatePath;
    }

    public function withTemplatePathFromNamespace(): self
    {
        $config = clone $this;
        $config->templatePath = Str::absolutePathFromNamespace($this->namespace, $this->rootNamespace, $this->rootDir);
        return $config;
    }

    public function withVoEntityNamespace(): self
    {
        $namespace = Str::replace($this->namespace, '\\VO\\', '\\VO\\Entity\\');
        $namespace = Str::replace($namespace, '\\Entity\\Entity\\', '\\Entity\\');

        $config = clone $this;
        $config->namespace = $namespace;
        return $config;
    }

    public function withTemplatePath(string $templatePath): self
    {
        $absoluteTemplatePath = __DIR__.'/../Resources/templates/'.$templatePath;
        Assert::fileExists($absoluteTemplatePath, 'Template file does not exist: '.$absoluteTemplatePath);

        $config = clone $this;
        $namespace = Str::prefixByRootNamespace(Str::namespaceFromPath($templatePath, $config->rootDir()), $config->rootNamespace());

        $config->templatePath = $absoluteTemplatePath;
        $config->namespace = $namespace;
        return $config;
    }

    public function withTemplatePathKeepingNamespace(string $templatePath): self
    {
        $config = clone $this;
        $namespace = $config->namespace;
        $config = $config->withTemplatePath($templatePath);
        $config->namespace = $namespace;
        return $config;
    }
}
