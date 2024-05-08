<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use function Symfony\Component\String\u;

class ControllerMakerConfiguration extends FromTemplateMakerConfiguration
{
    public string $entityPath = '';
    public string $formTypePath = '';
    public string $voPath = '';

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Controller')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function entityPath(string $entityPath): static
    {
        $this->entityPath = $entityPath;
        return $this;
    }

    public function withEntityPath(string $entityPath): static
    {
        return $this->entityPath($entityPath);
    }

    public function formTypePath(string $formTypePath): static
    {
        $this->formTypePath = $formTypePath;
        return $this;
    }

    public function withFormTypePath(string $formTypePath): static
    {
        return $this->formTypePath($formTypePath);
    }

    public function voPath(string $voPath): static
    {
        $this->voPath = $voPath;
        return $this;
    }

    public function withVoPath(string $voPath): static
    {
        return $this->voPath($voPath);
    }

    public function entityClassName(): string
    {
        return Str::prefixByRootNamespace(Str::namespaceFromPath($this->entityPath, $this->rootDir()), $this->rootNamespace());
    }

    public function formTypeClassName(): string
    {
        return Str::prefixByRootNamespace(Str::namespaceFromPath($this->formTypePath, $this->rootDir()), $this->rootNamespace());
    }

    public function voClassName(): string
    {
        return Str::prefixByRootNamespace(Str::namespaceFromPath($this->voPath, $this->rootDir()), $this->rootNamespace());
    }
}