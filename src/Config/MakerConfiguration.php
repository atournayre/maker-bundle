<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Helper\UStr;

abstract class MakerConfiguration implements MakerConfigurationInterface
{
    protected string $absolutePath;
    protected ?string $sourceCode = null;

    protected function __construct(
        public readonly string $rootNamespace,
        public readonly string $rootDir,
        public readonly string $namespace,
        public readonly string $className,
        public readonly string $fqcn,
    )
    {
        $this->absolutePath = Str::absolutePathFromNamespace($fqcn, $this->rootNamespace, $this->rootDir);
    }

    /**
     * @throws \Throwable
     */
    public static function fromNamespace(
        string $rootDir,
        string $rootNamespace,
        string $namespace,
        string $className,
    ): static
    {
        $fqcn = UStr::create($namespace)
            ->append('\\', $className)
            ->ensureEnd(static::classNameSuffix())
            ->toString();

        return static::fromFqcn(
            rootDir: $rootDir,
            rootNamespace: $rootNamespace,
            fqcn: $fqcn,
        );
    }

    /**
     * @throws \Throwable
     */
    public static function fromFqcn(
        string $rootDir,
        string $rootNamespace,
        string $fqcn,
    ): self
    {
        $fqcn = UStr::create($fqcn)
            ->ensureEnd(static::classNameSuffix())
            ->toString();

        $fqcnMap = Map::explode('\\', $fqcn);

        $namespace = $fqcnMap->copy()
            ->slice(0, -1)
            ->join('\\');

        $className = $fqcnMap->last();

        return new static(
            rootNamespace: $rootNamespace,
            rootDir: $rootDir,
            namespace: $namespace,
            className: $className,
            fqcn: $fqcn,
        );
    }

    public static function fromTemplate(
        string $rootDir,
        string $rootNamespace,
        string $templatePath,
    ): static
    {
        return static::fromFqcn(
            rootDir: $rootDir,
            rootNamespace: $rootNamespace,
            fqcn: Str::prefixByRootNamespace(Str::namespaceFromPath($templatePath, $rootDir), $rootNamespace),
        );
    }

    protected static function classNameSuffix(): string
    {
        return '';
    }


    public function namespace(): string
    {
        return $this->namespace;
    }

    public function classname(): string
    {
        return $this->className;
    }

    public function fqcn(): string
    {
        return $this->fqcn;
    }

    public function rootDir(): string
    {
        return $this->rootDir;
    }

    public function rootNamespace(): string
    {
        return $this->rootNamespace;
    }

    public function withSourceCode(string $sourceCode): self
    {
        $clone = clone $this;
        $clone->sourceCode = $sourceCode;
        return $clone;
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function sourceCode(): string
    {
        return $this->sourceCode ?? '';
    }

    public function allowedTypes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function propertiesAllowedTypes(): array
    {
        return [];
    }
}
