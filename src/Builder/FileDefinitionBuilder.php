<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\NamespacePath;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\TraitType;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

final class FileDefinitionBuilder
{
    private string $name;
    private string $nameSuffix;
    private string $rootDir;
    private NamespacePath $namespacePath;
    private string $normalizeName;
    private string $className;
    private string $absolutePath;
    public PhpFile $file;

    private function __construct(
        string $namespace,
        string $name,
        string $nameSuffix,
        string $rootNamespace,
        string $rootDir
    )
    {
        Assert::notEmpty($namespace);
        Assert::notEmpty($name);
        Assert::string($nameSuffix);
        Assert::notEmpty($rootNamespace);
        Assert::notEmpty($rootDir);

        $this->namespacePath = $this->defineNamespacePath($namespace, $rootNamespace);
        $this->name = $name;
        $this->nameSuffix = $nameSuffix;
        $this->normalizeName = NamespacePath::normalize($this->name);
        $this->className = $this->defineClassName();
        $this->rootDir = $rootDir;
        $this->absolutePath = $this->defineAbsoluteFilePath();
    }

    public static function build(
        string $namespace,
        string $name,
        string $nameSuffix,
        MakerConfig $config
    ): self
    {
        $fileDefinitionBuilder = new self($namespace, $name, $nameSuffix, $config->rootNamespace(), $config->rootDir());
        return $fileDefinitionBuilder->withDefaultFile();
    }

    private function withDefaultFile(): self
    {
        $clone = clone $this;
        $clone->file = new PhpFile;
        $clone->file->addComment('This file has been auto-generated');
        $clone->file->setStrictTypes();

        return $clone;
    }

    public function withFile(PhpFile $file): self
    {
        $clone = clone $this;
        $clone->file = $file;

        return $clone;
    }

    public function namespace(): string
    {
        return $this->namespacePath->toNamespace();
    }

    public function normalizeName(): string
    {
        return $this->normalizeName;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function fullName(): string
    {
        return $this->namespace() . '\\' . $this->className;
    }

    private function defineClassName(): string
    {
        return ClassNameBuilder::build($this->name)
            ->withSuffixe($this->nameSuffix)
            ->name();
    }

    private function defineNamespacePath(string $namespace, string $rootNamespace): NamespacePath
    {
        return new NamespacePath($namespace, $rootNamespace);
    }

    private function defineAbsoluteFilePath(): string
    {
        return u($this->rootDir)
            ->append('/src/')
            ->append($this->namespacePath->normalizedValue())
            ->append('/')
            ->append($this->className)
            ->append('.php')
            ->replace('\\', '/')
            ->toString();
    }

    public function getContent(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return (string)$this->file;
    }

    public function getClass(): ClassType|EnumType|InterfaceType|TraitType
    {
        return $this->file->getClasses()[$this->fullName()];
    }
}
