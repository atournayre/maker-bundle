<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Nette\PhpGenerator\PhpFile;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class FileDefinition
{
    private ?string $sourceCode = null;

    private function __construct(
        private readonly string $namespace,
        private readonly string $classname,
        private readonly string $absolutePath,
        private readonly string $builder,
        private readonly MakerConfig $configuration,
    )
    {
    }

    public static function create(MakerConfig $config): self
    {
        $fileDefinition = self::fromConfig($config);

        if (!$config->hasTemplatePath()) {
            return $fileDefinition;
        }

        return $fileDefinition->fromTemplatePath();
    }

    private function generateEmptyClassFromTemplatePath(string $templatePath): string
    {
        $classname = Str::namespaceFromPath($this->configuration->templatePath(), $this->configuration->rootDir());
        $namespace = Str::prefixByRootNamespace($classname, $this->configuration->rootNamespace());
        $phpFile = new PhpFile;
        $phpFile->addComment('This file has been auto-generated');
        $phpFile->addComment(Str::sprintf('Template "%s" not found, creating an empty file', $templatePath));
        $phpFile->addClass($namespace);

        return (string)$phpFile;
    }

    private function fromTemplatePath(): self
    {
        $templateExists = file_exists($this->configuration->templatePath());

        $content = $templateExists
            ? file_get_contents($this->configuration->templatePath())
            : $this->generateEmptyClassFromTemplatePath($this->configuration->templatePath());

        $phpFile = PhpFile::fromCode($content)
            ->addComment('This file has been auto-generated');

        return $this->withSourceCode((string)$phpFile);
    }

    private static function fromConfig(MakerConfig $config): self
    {
        Assert::notEmpty($config->rootDir(), 'Root directory must be set in MakerConfig');
        Assert::notEmpty($config->namespace(), 'Namespace must be set in MakerConfig');

        $namespace = u($config->namespace());
        $namespaceWithoutClassName = Str::namespaceWithoutClassname($config->namespace());
        $classname = $namespace->afterLast('\\');

        $absolutePath = Str::absolutePathFromNamespace($config->namespace(), $config->rootNamespace(), $config->rootDir());

        return new self(
            $namespaceWithoutClassName,
            $classname->toString(),
            $absolutePath,
            $config->generator(),
            $config,
        );
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function classname(): string
    {
        return $this->classname;
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function sourceCode(): ?string
    {
        return $this->sourceCode;
    }

    public function builder(): string
    {
        return $this->builder;
    }

    public function configuration(): MakerConfig
    {
        return $this->configuration;
    }

    public function uniqueIdentifier(): string
    {
        return $this->absolutePath;
    }

    public function withSourceCode(string $sourceCode): self
    {
        $fileDefinition = clone $this;
        $fileDefinition->sourceCode = $sourceCode;
        return $fileDefinition;
    }

    public function toPhpFile(): PhpFile
    {
        if (null !== $this->sourceCode) {
            return PhpFile::fromCode($this->sourceCode);
        }

        $absolutePath = Str::absolutePathFromNamespace($this->namespace(), $this->configuration->rootNamespace(), $this->configuration->rootDir());
        $sourceCode = file_get_contents($absolutePath);

        return PhpFile::fromCode($sourceCode);
    }

    public function fullName(): string
    {
        return $this->namespace() . '\\' . $this->classname();
    }
}
