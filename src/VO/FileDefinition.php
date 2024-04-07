<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
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
    )
    {
    }

    public static function fromConfig(MakerConfig $config): self
    {
        Assert::notEmpty($config->rootDir(), 'Root directory must be set in MakerConfig');
        Assert::notEmpty($config->namespace(), 'Namespace must be set in MakerConfig');

        $namespace = u($config->namespace())->replace('/', '\\')->beforeLast('\\');
        $classnameSuffix = $config->classnameSuffix() ?? '';

        $classname = u($config->namespace())
            ->afterLast('\\')
            ->ensureEnd($classnameSuffix)
        ;

        $rootDir = u($config->rootDir())->ensureEnd('/src/');

        $absolutePath = u($config->namespace())
            ->replace($config->rootNamespace(), $rootDir->toString())
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php')
        ;

        return new self(
            $namespace->toString(),
            $classname->toString(),
            $absolutePath->toString(),
            $config->generator(),
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
        Assert::notEmpty($this->sourceCode, 'Source code must be set before converting to PhpFile');

        return PhpFile::fromCode($this->sourceCode);
    }
}
