<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Helper\Str;

class ControllerMakerConfiguration extends FromTemplateMakerConfiguration
{
    public string $entityPath = '';

    public string $formTypePath = '';

    public string $voPath = '';

    public ?string $entityNamespace = null;

    public ?string $formTypeNamespace = null;

    public ?string $voNamespace = null;

    protected static function classNameSuffix(): string
    {
        return 'Controller';
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

    public function entityNamespace(): string
    {
        return $this->entityNamespace ?? Str::prefixByRootNamespace(Str::namespaceFromPath($this->entityPath, $this->rootDir()), $this->rootNamespace());
    }

    public function formTypeNamespace(): string
    {
        return $this->formTypeNamespace ?? Str::prefixByRootNamespace(Str::namespaceFromPath($this->formTypePath, $this->rootDir()), $this->rootNamespace());
    }

    public function voNamespace(): string
    {
        return $this->voNamespace ?? Str::prefixByRootNamespace(Str::namespaceFromPath($this->voPath, $this->rootDir()), $this->rootNamespace());
    }

    public function entityClassName(): string
    {
        return Str::classNameFromNamespace($this->entityNamespace, '');
    }

    public function formTypeClassName(): string
    {
        return Str::classNameFromNamespace($this->formTypeNamespace, '');
    }

    public function voClassName(): string
    {
        return Str::classNameFromNamespace($this->voNamespace, '');
    }

    public function withEntityNamespace(string $entityNamespace): static
    {
        $this->entityNamespace = $entityNamespace;
        return $this;
    }

    public function withFormTypeNamespace(string $formTypeNamespace): static
    {
        $this->formTypeNamespace = $formTypeNamespace;
        return $this;
    }

    public function withVoNamespace(string $voNamespace): static
    {
        $this->voNamespace = $voNamespace;
        return $this;
    }
}
